const { test, expect } = require('@playwright/test');
const { attachJemDebugMonitor } = require('../helpers/jem-debug-monitor');
const {
  CRUD_ENABLED,
  baseURL,
  loginToJoomlaAdministrator,
  futureDate,
  createCategory,
  createEvent,
  openAssignments,
  openAssignment,
  openNewAssignment,
  registryData,
  selectAssignmentValues,
  createAssignment,
  saveAssignment,
  removeAssignment,
  removeJemRecord,
  cleanupStep,
  openFrontendEvent,
  presentationStyles,
  expectStyleSet,
  submitTask,
} = require('./helpers-jempresentation');

test.describe.serial('JEM Presentation v0.1.6 — beheerregistry en runtimecontract', () => {
  test.skip(!CRUD_ENABLED, 'Zet JEM_ENABLE_CRUD=1 voor autonome JEM Presentation fixtures.');
  test.describe.configure({ timeout: 180_000 });

  let adminContext;
  let admin;
  let categoryId = 0;
  let assignedEvent;
  let unassignedEvent;
  let assignmentId = 0;

  test.beforeAll(async ({ browser }, testInfo) => {
    testInfo.setTimeout(300_000);

    adminContext = await browser.newContext();
    admin = await adminContext.newPage();
    await loginToJoomlaAdministrator(admin);

    const stamp = `${Date.now()}-${Math.floor(Math.random() * 10000)}`;
    categoryId = await createCategory(admin, `PW-JP-CAT-${stamp}`);
    const date = futureDate(60);

    assignedEvent = await createEvent(admin, {
      title: `PW-JP-ASSIGNED-${stamp}`,
      categoryId,
      date,
    });

    unassignedEvent = await createEvent(admin, {
      title: `PW-JP-UNASSIGNED-${stamp}`,
      categoryId,
      date,
    });

    assignmentId = await createAssignment(admin, {
      eventId: assignedEvent.eventId,
      eventTitle: assignedEvent.title,
      profile: 'modern',
      layout: 'standard',
    });
  });

  test.afterAll(async ({}, testInfo) => {
    testInfo.setTimeout(360_000);

    if (!adminContext) return;

    const cleanupPage = adminContext.pages()[0] || await adminContext.newPage();

    await cleanupStep(
      'administrator sessie herstellen',
      () => loginToJoomlaAdministrator(cleanupPage),
      45_000
    );

    await cleanupStep(
      'JEM Presentation assignment verwijderen',
      () => removeAssignment(cleanupPage, assignmentId),
      50_000
    );

    await cleanupStep(
      'niet-gekoppeld JEM-event verwijderen',
      () => removeJemRecord(
        cleanupPage,
        'events',
        unassignedEvent?.eventId,
        'events.trash',
        'events.remove'
      ),
      50_000
    );

    await cleanupStep(
      'gekoppeld JEM-event verwijderen',
      () => removeJemRecord(
        cleanupPage,
        'events',
        assignedEvent?.eventId,
        'events.trash',
        'events.remove'
      ),
      50_000
    );

    await cleanupStep(
      'JEM-categorie verwijderen',
      () => removeJemRecord(
        cleanupPage,
        'categories',
        categoryId,
        'categories.trash',
        'categories.remove'
      ),
      50_000
    );

    await adminContext.close().catch(() => {});
    adminContext = null;
  });

  test('assignmentlijst opent gezond en toont de integratiekolom', async ({ page }, testInfo) => {
    await loginToJoomlaAdministrator(page);
    const monitor = await attachJemDebugMonitor(page, testInfo, { baseURL: baseURL() });

    await openAssignments(page);

    const headers = page.locator('table thead th');
    await expect(headers).toHaveCount(6);
    await expect(headers.nth(4)).toContainText(/integrat/i);
    await expect(page.locator('table tbody tr', { hasText: assignedEvent.title }).first()).toBeVisible();

    await monitor.assertHealthy('JEM Presentation assignmentlijst');
  });

  test('bestaand assignment opent met canonieke Modern + Standard waarden', async ({ page }) => {
    await loginToJoomlaAdministrator(page);
    await openAssignment(page, assignmentId);

    await expect(page.locator('#jform_context_id')).toHaveValue(String(assignedEvent.eventId));
    await expect(page.locator('#jform_profile')).toHaveValue('modern');
    await expect(page.locator('#jform_layout')).toHaveValue('standard');
  });

  test('Standard toont bevestigde registry-metadata en de Standard-preview', async ({ page }) => {
    await loginToJoomlaAdministrator(page);
    await openAssignment(page, assignmentId);
    await selectAssignmentValues(page, { profile: 'modern', layout: 'standard' });

    const registry = await registryData(page);
    expect(registry.profiles.modern.id).toBe('modern');
    expect(registry.layouts.standard.id).toBe('standard');
    expect(registry.layouts.standard.status).toBe('confirmed');
    expect(registry.layouts.standard.integration).toBe('native');
    expect(registry.layouts.standard.bridge).toBe(false);

    await expect(page.locator('#jempresentation-layout-title')).toHaveText(registry.layouts.standard.label);
    await expect(page.locator('#jempresentation-layout-status')).toHaveText(registry.layouts.standard.statusLabel);
    await expect(page.locator('#jempresentation-integration-meta')).toHaveText(registry.layouts.standard.integrationLabel);
    await expect(page.locator('#jempresentation-bridge-meta')).toHaveText(registry.layouts.standard.bridgeLabel);
    await expect(page.locator('[data-jempresentation-preview="standard"]')).toBeVisible();
    await expect(page.locator('#jempresentation-runtime-warning')).toHaveClass(/d-none/);
  });

  test('wisselen naar Hero actualiseert metadata en preview zonder submit of navigatie', async ({ page }) => {
    await loginToJoomlaAdministrator(page);
    await openAssignment(page, assignmentId);
    const registry = await registryData(page);
    const before = page.url();

    await page.locator('#jform_layout').selectOption('hero', { force: true });

    expect(page.url()).toBe(before);
    await expect(page.locator('#jempresentation-layout-title')).toHaveText(registry.layouts.hero.label);
    await expect(page.locator('#jempresentation-layout-status')).toHaveText(registry.layouts.hero.statusLabel);
    await expect(page.locator('#jempresentation-integration-meta')).toHaveText(registry.layouts.hero.integrationLabel);
    await expect(page.locator('#jempresentation-bridge-meta')).toHaveText(registry.layouts.hero.bridgeLabel);
    await expect(page.locator('[data-jempresentation-preview="hero"]')).toBeVisible();
    await expect(page.locator('[data-jempresentation-preview="standard"]')).toBeHidden();
  });

  test('wisselen naar Twee kolommen toont native integratie zonder bridge', async ({ page }) => {
    await loginToJoomlaAdministrator(page);
    await openAssignment(page, assignmentId);
    const registry = await registryData(page);

    await page.locator('#jform_layout').selectOption('two-column', { force: true });

    expect(registry.layouts['two-column'].status).toBe('confirmed');
    expect(registry.layouts['two-column'].integration).toBe('native');
    expect(registry.layouts['two-column'].bridge).toBe(false);
    await expect(page.locator('#jempresentation-integration-meta')).toHaveText(registry.layouts['two-column'].integrationLabel);
    await expect(page.locator('#jempresentation-bridge-meta')).toHaveText(registry.layouts['two-column'].bridgeLabel);
    await expect(page.locator('[data-jempresentation-preview="two-column"]')).toBeVisible();
  });

  test('gepland Sports-profiel wordt niet als runtime-beschikbaar voorgesteld', async ({ page }) => {
    await loginToJoomlaAdministrator(page);
    await openAssignment(page, assignmentId);
    const registry = await registryData(page);

    await selectAssignmentValues(page, { profile: 'sports', layout: 'standard' });

    expect(registry.profiles.sports.status).toBe('planned');
    await expect(page.locator('#jempresentation-profile-meta')).toContainText(registry.profiles.sports.statusLabel);
    await expect(page.locator('#jempresentation-runtime-warning')).toBeVisible();
    await expect(page.locator('#jempresentation-runtime-warning')).toHaveText(registry.labels.unsupported);
  });

  test('geplande Route-layout toont waarschuwing en eigen schematische preview', async ({ page }) => {
    await loginToJoomlaAdministrator(page);
    await openAssignment(page, assignmentId);
    const registry = await registryData(page);

    await selectAssignmentValues(page, { profile: 'modern', layout: 'route' });

    expect(registry.layouts.route.status).toBe('planned');
    expect(registry.layouts.route.integration).toBe('planned');
    await expect(page.locator('#jempresentation-runtime-warning')).toBeVisible();
    await expect(page.locator('[data-jempresentation-preview="route"]')).toBeVisible();
  });

  test('Hero opslaan bewaart de canonieke layout-ID en toont Bridge in de lijst', async ({ page }) => {
    await loginToJoomlaAdministrator(page);
    await saveAssignment(page, assignmentId, { profile: 'modern', layout: 'hero' }, 'assignment.save');

    await openAssignments(page);
    const row = page.locator('table tbody tr', { hasText: assignedEvent.title }).first();
    await expect(row).toBeVisible();
    await expect(row).toContainText(/Bridge/i);

    await openAssignment(page, assignmentId);
    await expect(page.locator('#jform_profile')).toHaveValue('modern');
    await expect(page.locator('#jform_layout')).toHaveValue('hero');
  });

  test('nieuw formulier voor hetzelfde event maakt geen dubbel assignment', async ({ page }) => {
    await loginToJoomlaAdministrator(page);

    await openNewAssignment(page);
    await selectAssignmentValues(page, {
      eventId: assignedEvent.eventId,
      profile: 'modern',
      layout: 'two-column',
    });
    await submitTask(page, 'assignment.save');

    await openAssignments(page);
    const rows = page.locator('table tbody tr', { hasText: assignedEvent.title });
    await expect(rows).toHaveCount(1);

    const rowAssignmentId = Number(await rows.first().locator('input[name="cid[]"]').getAttribute('value'));
    expect(rowAssignmentId).toBe(assignmentId);

    await openAssignment(page, assignmentId);
    await expect(page.locator('#jform_layout')).toHaveValue('two-column');
  });

  test('integration status toont runtime, template, bridge en native-API status zonder technische fout', async ({ page }, testInfo) => {
    await loginToJoomlaAdministrator(page);
    const monitor = await attachJemDebugMonitor(page, testInfo, { baseURL: baseURL() });

    await openAssignment(page, assignmentId);
    const status = page.locator('.jempresentation-status-list');
    await expect(status).toBeVisible();
    await expect(status.locator(':scope > div')).toHaveCount(4);

    const values = status.locator('dd');
    await expect(values).toHaveCount(4);
    for (let index = 0; index < 4; index += 1) {
      expect((await values.nth(index).innerText()).trim()).not.toBe('');
    }

    await monitor.assertHealthy('JEM Presentation integration status');
  });

  test('Standard laadt alleen Modern CSS en event zonder assignment krijgt geen Presentation-assets', async ({ page }, testInfo) => {
    await loginToJoomlaAdministrator(admin);
    await saveAssignment(admin, assignmentId, { profile: 'modern', layout: 'standard' }, 'assignment.apply');

    const monitor = await attachJemDebugMonitor(page, testInfo, { baseURL: baseURL() });

    await openFrontendEvent(page, assignedEvent.eventId, { debug: true });
    expectStyleSet(await presentationStyles(page), { modern: true, hero: false, twoColumn: false });

    await openFrontendEvent(page, unassignedEvent.eventId, { debug: true });
    expectStyleSet(await presentationStyles(page), { modern: false, hero: false, twoColumn: false });

    await monitor.assertHealthy('JEM Presentation Standard + niet-gekoppeld event');
  });

  test('Hero runtime laadt Modern + Hero CSS en geen Two Column CSS', async ({ page }, testInfo) => {
    await loginToJoomlaAdministrator(admin);
    await saveAssignment(admin, assignmentId, { profile: 'modern', layout: 'hero' }, 'assignment.apply');

    const monitor = await attachJemDebugMonitor(page, testInfo, { baseURL: baseURL() });
    await openFrontendEvent(page, assignedEvent.eventId, { debug: true });
    expectStyleSet(await presentationStyles(page), { modern: true, hero: true, twoColumn: false });
    await monitor.assertHealthy('JEM Presentation Hero runtime-assets');
  });

  test('Twee kolommen runtime laadt Modern + Two Column CSS en geen Hero CSS', async ({ page }, testInfo) => {
    await loginToJoomlaAdministrator(admin);
    await saveAssignment(admin, assignmentId, { profile: 'modern', layout: 'two-column' }, 'assignment.apply');

    const monitor = await attachJemDebugMonitor(page, testInfo, { baseURL: baseURL() });
    await openFrontendEvent(page, assignedEvent.eventId, { debug: true });
    expectStyleSet(await presentationStyles(page), { modern: true, hero: false, twoColumn: true });
    await monitor.assertHealthy('JEM Presentation Two Column runtime-assets');
  });
});
