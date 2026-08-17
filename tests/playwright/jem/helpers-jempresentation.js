const { expect } = require('@playwright/test');
const {
  baseURL,
  gotoStable,
  loginToJoomlaAdministrator,
  fillCalendar,
} = require('./helpers-registration');

const CRUD_ENABLED = /^(1|true|yes)$/i.test(process.env.JEM_ENABLE_CRUD || '');

function futureDate(days = 60) {
  const date = new Date();
  date.setUTCDate(date.getUTCDate() + days);
  return date.toISOString().slice(0, 10);
}

async function submitTask(page, task) {
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 30_000 }).catch(() => null),
    page.evaluate((joomlaTask) => {
      const form = document.getElementById('adminForm')
        || document.getElementById('assignment-form')
        || document.querySelector('form[name="adminForm"], form');

      if (!form) {
        throw new Error(`Geen Joomla-formulier gevonden voor ${joomlaTask}.`);
      }

      if (window.Joomla && typeof window.Joomla.submitbutton === 'function') {
        window.Joomla.submitbutton(joomlaTask);
        return;
      }

      let taskField = form.querySelector('input[name="task"]');
      if (!taskField) {
        taskField = document.createElement('input');
        taskField.type = 'hidden';
        taskField.name = 'task';
        form.appendChild(taskField);
      }

      taskField.value = joomlaTask;
      if (typeof form.requestSubmit === 'function') form.requestSubmit();
      else form.submit();
    }, task),
  ]);
}

async function searchJemList(page, marker) {
  const field = page
    .locator('#filter_search, input[name="filter_search"], input[name="filter[search]"]')
    .first();

  await expect(field).toBeVisible();
  await field.fill(marker);

  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 30_000 }).catch(() => null),
    field.press('Enter'),
  ]);
}

async function createCategory(page, name) {
  await gotoStable(page, `${baseURL()}/administrator/index.php?option=com_jem&task=category.add`);
  await page.locator('[name="jform[catname]"]').fill(name);
  await submitTask(page, 'category.save');

  await gotoStable(page, `${baseURL()}/administrator/index.php?option=com_jem&view=categories`);
  await searchJemList(page, name);

  const row = page.locator('table tbody tr', { hasText: name }).first();
  await expect(row).toBeVisible();

  const id = Number(await row.locator('input[name="cid[]"]').getAttribute('value'));
  expect(id).toBeGreaterThan(0);
  return id;
}

async function createEvent(page, { title, categoryId, date = futureDate() }) {
  await gotoStable(page, `${baseURL()}/administrator/index.php?option=com_jem&task=event.add`);
  await page.locator('[name="jform[title]"]').fill(title);
  await fillCalendar(page, 'jform[dates]', date);
  await fillCalendar(page, 'jform[enddates]', date);
  await page.locator('select[name="jform[cats][]"]').selectOption(String(categoryId), { force: true });
  await submitTask(page, 'event.save');

  await gotoStable(page, `${baseURL()}/administrator/index.php?option=com_jem&view=events`);
  await searchJemList(page, title);

  const row = page.locator('table tbody tr', { hasText: title }).first();
  await expect(row).toBeVisible();

  const eventId = Number(await row.locator('input[name="cid[]"]').getAttribute('value'));
  expect(eventId).toBeGreaterThan(0);

  return { eventId, title, categoryId, date };
}

async function openAssignments(page) {
  await gotoStable(page, `${baseURL()}/administrator/index.php?option=com_jempresentation&view=assignments`);
  await expect(page.locator('form#adminForm')).toBeVisible();
}

async function openAssignment(page, assignmentId) {
  await gotoStable(
    page,
    `${baseURL()}/administrator/index.php?option=com_jempresentation&task=assignment.edit&id=${assignmentId}`
  );
  await expect(page.locator('#assignment-form')).toBeVisible();
}

async function openNewAssignment(page) {
  await gotoStable(page, `${baseURL()}/administrator/index.php?option=com_jempresentation&task=assignment.add`);
  await expect(page.locator('#assignment-form')).toBeVisible();
}

async function registryData(page) {
  const node = page.locator('#jempresentation-registry-data');
  await expect(node).toBeAttached();
  return JSON.parse(await node.textContent());
}

async function selectAssignmentValues(page, { eventId, profile, layout }) {
  if (eventId !== undefined) {
    await page.locator('#jform_context_id').selectOption(String(eventId), { force: true });
  }
  if (profile !== undefined) {
    await page.locator('#jform_profile').selectOption(String(profile), { force: true });
  }
  if (layout !== undefined) {
    await page.locator('#jform_layout').selectOption(String(layout), { force: true });
  }
}

async function createAssignment(page, {
  eventId,
  eventTitle,
  profile = 'modern',
  layout = 'standard',
}) {
  await openNewAssignment(page);
  await selectAssignmentValues(page, { eventId, profile, layout });
  await submitTask(page, 'assignment.save');

  await openAssignments(page);
  const row = page.locator('table tbody tr', { hasText: eventTitle }).first();
  await expect(row).toBeVisible();

  const assignmentId = Number(await row.locator('input[name="cid[]"]').getAttribute('value'));
  expect(assignmentId).toBeGreaterThan(0);
  return assignmentId;
}

async function saveAssignment(page, assignmentId, values, task = 'assignment.apply') {
  await openAssignment(page, assignmentId);
  await selectAssignmentValues(page, values);
  await submitTask(page, task);

  if (task === 'assignment.apply') {
    await expect(page.locator('#assignment-form')).toBeVisible();
  }
}

async function removeAssignment(page, assignmentId) {
  if (!assignmentId) return;

  await openAssignments(page);

  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 30_000 }).catch(() => null),
    page.evaluate(({ id }) => {
      const form = document.getElementById('adminForm');
      if (!form) throw new Error('Geen assignmentlijst-formulier gevonden tijdens cleanup.');

      form.querySelectorAll('input[name="cid[]"]').forEach((node) => {
        node.checked = String(node.value) === String(id);
      });

      let task = form.querySelector('input[name="task"]');
      if (!task) {
        task = document.createElement('input');
        task.type = 'hidden';
        task.name = 'task';
        form.appendChild(task);
      }
      task.value = 'assignments.delete';

      const boxchecked = form.querySelector('input[name="boxchecked"]');
      if (boxchecked) boxchecked.value = '1';
      form.submit();
    }, { id: assignmentId }),
  ]);
}

async function removeJemRecord(page, view, id, trashTask, removeTask) {
  if (!id) return;

  for (const [state, task] of [['*', trashTask], ['-2', removeTask]]) {
    await gotoStable(
      page,
      `${baseURL()}/administrator/index.php?option=com_jem&view=${view}&filter_state=${state}`
    );

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 30_000 }).catch(() => null),
      page.evaluate(({ recordId, joomlaTask }) => {
        const form = document.getElementById('adminForm');
        if (!form) throw new Error(`Geen JEM ${joomlaTask}-formulier gevonden tijdens cleanup.`);

        form.querySelectorAll('input[name="cid[]"]').forEach((node) => node.remove());

        const cid = document.createElement('input');
        cid.type = 'hidden';
        cid.name = 'cid[]';
        cid.value = String(recordId);
        form.appendChild(cid);

        let taskField = form.querySelector('input[name="task"]');
        if (!taskField) {
          taskField = document.createElement('input');
          taskField.type = 'hidden';
          taskField.name = 'task';
          form.appendChild(taskField);
        }
        taskField.value = joomlaTask;

        const boxchecked = form.querySelector('input[name="boxchecked"]');
        if (boxchecked) boxchecked.value = '1';
        form.submit();
      }, { recordId: id, joomlaTask: task }),
    ]);
  }
}

async function cleanupStep(label, action, timeoutMs = 45_000) {
  let timer;

  try {
    await Promise.race([
      Promise.resolve().then(action),
      new Promise((_, reject) => {
        timer = setTimeout(
          () => reject(new Error(`Cleanup timeout: ${label}`)),
          timeoutMs
        );
      }),
    ]);
  } catch (error) {
    console.warn(`[JEM Presentation cleanup] ${label}: ${error.message}`);
  } finally {
    clearTimeout(timer);
  }
}

async function openFrontendEvent(page, eventId, { debug = false } = {}) {
  const suffix = debug ? '&jempresentation_debug=1' : '';
  const response = await gotoStable(
    page,
    `${baseURL()}/index.php?option=com_jem&view=event&id=${eventId}${suffix}`
  );

  if (response) expect(response.status()).toBeLessThan(500);
  await expect(page.locator('main, [role="main"]').first()).toBeVisible();
  return response;
}

async function presentationStyles(page) {
  return page.locator('link[rel="stylesheet"]').evaluateAll((nodes) => nodes
    .map((node) => node.getAttribute('href') || '')
    .filter((href) => /plg_system_jempresentationruntime\/css\//i.test(href)));
}

function expectStyleSet(styles, { modern, hero, twoColumn }) {
  const has = (name) => styles.some((href) => href.includes(`/css/${name}.css`));
  expect(has('modern')).toBe(modern);
  expect(has('hero')).toBe(hero);
  expect(has('two-column')).toBe(twoColumn);
}

module.exports = {
  CRUD_ENABLED,
  baseURL,
  gotoStable,
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
};
