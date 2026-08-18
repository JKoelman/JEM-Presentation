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
  removeAssignment,
  removeJemRecord,
  cleanupStep,
  submitTask,
} = require('./helpers-jempresentation');

async function forceSelect(page, selector, value, label = value) {
  await page.locator(selector).evaluate((select, payload) => {
    let option = Array.from(select.options).find((entry) => entry.value === payload.value);
    if (!option) {
      option = document.createElement('option');
      option.value = payload.value;
      option.textContent = payload.label;
      select.appendChild(option);
    }
    option.disabled = false;
    select.value = payload.value;
    select.dispatchEvent(new Event('change', { bubbles: true }));
  }, { value: String(value), label: String(label) });
}

test.describe.serial('JEM Presentation v0.1.7 — component hardening', () => {
  test.skip(!CRUD_ENABLED, 'Zet JEM_ENABLE_CRUD=1 voor autonome JEM Presentation fixtures.');
  test.describe.configure({ timeout: 180_000 });

  let adminContext;
  let admin;
  let categoryId = 0;
  let assignedEvent;
  let validationEvent;
  let orphanEvent;
  let assignmentId = 0;
  let orphanAssignmentId = 0;
  let orphanEventRemoved = false;

  test.beforeAll(async ({ browser }, testInfo) => {
    testInfo.setTimeout(360_000);

    adminContext = await browser.newContext();
    admin = await adminContext.newPage();
    await loginToJoomlaAdministrator(admin);

    const stamp = `${Date.now()}-${Math.floor(Math.random() * 10000)}`;
    categoryId = await createCategory(admin, `PW-JP17-CAT-${stamp}`);
    const date = futureDate(75);

    assignedEvent = await createEvent(admin, {
      title: `PW-JP17-ASSIGNED-${stamp}`,
      categoryId,
      date,
    });

    validationEvent = await createEvent(admin, {
      title: `PW-JP17-VALIDATE-${stamp}`,
      categoryId,
      date,
    });

    orphanEvent = await createEvent(admin, {
      title: `PW-JP17-ORPHAN-${stamp}`,
      categoryId,
      date,
    });

    assignmentId = await createAssignment(admin, {
      eventId: assignedEvent.eventId,
      eventTitle: assignedEvent.title,
      profile: 'modern',
      layout: 'standard',
    });

    orphanAssignmentId = await createAssignment(admin, {
      eventId: orphanEvent.eventId,
      eventTitle: orphanEvent.title,
      profile: 'modern',
      layout: 'standard',
    });
  });

  test.afterAll(async ({}, testInfo) => {
    testInfo.setTimeout(420_000);
    if (!adminContext) return;

    const cleanupPage = adminContext.pages()[0] || await adminContext.newPage();

    await cleanupStep('administrator sessie herstellen', () => loginToJoomlaAdministrator(cleanupPage));
    await cleanupStep('primary assignment verwijderen', () => removeAssignment(cleanupPage, assignmentId), 50_000);
    await cleanupStep('orphan assignment verwijderen', () => removeAssignment(cleanupPage, orphanAssignmentId), 50_000);

    await cleanupStep(
      'validatie-event verwijderen',
      () => removeJemRecord(cleanupPage, 'events', validationEvent?.eventId, 'events.trash', 'events.remove'),
      50_000
    );
    await cleanupStep(
      'gekoppeld event verwijderen',
      () => removeJemRecord(cleanupPage, 'events', assignedEvent?.eventId, 'events.trash', 'events.remove'),
      50_000
    );

    if (!orphanEventRemoved) {
      await cleanupStep(
        'orphan event verwijderen',
        () => removeJemRecord(cleanupPage, 'events', orphanEvent?.eventId, 'events.trash', 'events.remove'),
        50_000
      );
    }

    await cleanupStep(
      'JEM-categorie verwijderen',
      () => removeJemRecord(cleanupPage, 'categories', categoryId, 'categories.trash', 'categories.remove'),
      50_000
    );

    await adminContext.close().catch(() => {});
    adminContext = null;
  });

  test('registry publiceert capabilities en geplande keuzes zijn niet beschikbaar voor nieuwe assignments', async ({ page }) => {
    await loginToJoomlaAdministrator(page);
    await openNewAssignment(page);

    const registry = await registryData(page);
    expect(registry.profiles.modern.supportedLayouts).toEqual(['standard', 'hero', 'two-column']);
    expect(registry.profiles.modern.selectable).toBe(true);
    expect(registry.profiles.sports.selectable).toBe(false);
    expect(registry.layouts.route.selectable).toBe(false);

    await expect(page.locator('#jform_profile option[value="sports"]')).toBeDisabled();
    await expect(page.locator('#jform_layout option[value="route"]')).toBeDisabled();
  });

  test('eventselector markeert reeds gekoppelde events en blokkeert normale dubbele selectie', async ({ page }) => {
    await loginToJoomlaAdministrator(page);
    await openNewAssignment(page);

    const assigned = page.locator(`#jform_context_id option[value="${assignedEvent.eventId}"]`);
    const free = page.locator(`#jform_context_id option[value="${validationEvent.eventId}"]`);

    await expect(assigned).toBeDisabled();
    await expect(assigned).toContainText(/assigned|koppeling/i);
    await expect(free).not.toBeDisabled();
  });

  test('crafted duplicate-new save hergebruikt bestaand assignment en meldt dit expliciet', async ({ page }) => {
    await loginToJoomlaAdministrator(page);
    await openNewAssignment(page);

    await forceSelect(page, '#jform_context_id', assignedEvent.eventId, assignedEvent.title);
    await selectAssignmentValues(page, { profile: 'modern', layout: 'standard' });
    await submitTask(page, 'assignment.save');

    await expect(page.getByText(/bestaande koppeling.*bijgewerkt|existing assignment.*updated/i).first()).toBeVisible();

    const rows = page.locator('table tbody tr', { hasText: assignedEvent.title });
    await expect(rows).toHaveCount(1);
  });

  test('server weigert een onbekend presentatieprofiel', async ({ page }) => {
    await loginToJoomlaAdministrator(page);
    await openNewAssignment(page);

    await selectAssignmentValues(page, { eventId: validationEvent.eventId, layout: 'standard' });
    await forceSelect(page, '#jform_profile', 'pw-unknown-profile', 'PW unknown profile');
    await submitTask(page, 'assignment.apply');

    await expect(page.getByText(/onbekend presentatieprofiel|unknown presentation profile/i).first()).toBeVisible();
  });

  test('server weigert een geplande of niet-ondersteunde profiel-layoutcombinatie', async ({ page }) => {
    await loginToJoomlaAdministrator(page);
    await openNewAssignment(page);

    await selectAssignmentValues(page, { eventId: validationEvent.eventId, profile: 'modern' });
    await forceSelect(page, '#jform_layout', 'route', 'Route / GPX');
    await submitTask(page, 'assignment.apply');

    await expect(page.getByText(/combinatie.*niet beschikbaar|combination.*not available/i).first()).toBeVisible();
  });

  test('server weigert een niet-bestaand JEM-event', async ({ page }) => {
    await loginToJoomlaAdministrator(page);
    await openNewAssignment(page);

    await forceSelect(page, '#jform_context_id', 999999999, 'Missing test event');
    await selectAssignmentValues(page, { profile: 'modern', layout: 'standard' });
    await submitTask(page, 'assignment.apply');

    await expect(page.getByText(/999999999.*bestaat niet|999999999.*does not exist|JEM-event #999999999|JEM event #999999999/i).first()).toBeVisible();
  });

  test('verwijderd JEM-event blijft als expliciete orphan Presentation-koppeling zichtbaar', async ({ page }) => {
    await loginToJoomlaAdministrator(page);

    await removeJemRecord(page, 'events', orphanEvent.eventId, 'events.trash', 'events.remove');
    orphanEventRemoved = true;

    await openAssignments(page);
    const row = page.locator('table tbody tr', { hasText: String(orphanEvent.eventId) }).first();
    await expect(row).toBeVisible();
    await expect(row).toContainText(/ontbrekend JEM-event|missing JEM event/i);
    await expect(row).toHaveClass(/table-warning/);
  });

  test('integration status benoemt standaard site-template en toont bridgebestanden afzonderlijk', async ({ page }, testInfo) => {
    await loginToJoomlaAdministrator(page);
    const monitor = await attachJemDebugMonitor(page, testInfo, { baseURL: baseURL() });

    await openAssignment(page, assignmentId);

    await expect(page.getByText(/standaard site-template|default site template/i).first()).toBeVisible();
    await expect(page.getByText('html/com_jem/event/default.php', { exact: true })).toBeVisible();
    await expect(page.getByText('html/com_jem/event/responsive/default.php', { exact: true })).toBeVisible();

    await monitor.assertHealthy('JEM Presentation v0.1.7 integration diagnostics');
  });
});
