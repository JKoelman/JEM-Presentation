<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$registryJson = json_encode(
    $this->registryData,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);

$statusClass = static function (string $state): string {
    return match ($state) {
        'active', 'available', 'detected', 'not-required' => 'success',
        'inactive', 'missing', 'incomplete', 'unsupported-template', 'not-detected' => 'warning',
        'conflict' => 'danger',
        default => 'secondary',
    };
};
?>
<form
    action="<?php echo Route::_('index.php?option=com_jempresentation&layout=edit&id=' . (int) ($this->item->id ?? 0)); ?>"
    method="post"
    name="adminForm"
    id="assignment-form"
    class="form-validate"
>
    <div class="row g-4">
        <div class="col-12 col-xl-7">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="h5 mb-0"><?php echo Text::_('COM_JEMPRESENTATION_ASSIGNMENT_DETAILS'); ?></h2>
                </div>
                <div class="card-body">
                    <?php echo $this->form->renderFieldset('details'); ?>

                    <div id="jempresentation-selection-info" class="jempresentation-selection-info mt-4" aria-live="polite">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">
                                    <?php echo Text::_('COM_JEMPRESENTATION_LAYOUT_INFORMATION'); ?>
                                </div>
                                <h3 id="jempresentation-layout-title" class="h5 mb-0"></h3>
                            </div>
                            <span id="jempresentation-layout-status" class="badge text-bg-secondary"></span>
                        </div>

                        <p id="jempresentation-layout-description" class="mb-3"></p>

                        <dl class="row mb-3 jempresentation-meta-list">
                            <dt class="col-sm-4"><?php echo Text::_('COM_JEMPRESENTATION_PROFILE'); ?></dt>
                            <dd id="jempresentation-profile-meta" class="col-sm-8"></dd>

                            <dt class="col-sm-4"><?php echo Text::_('COM_JEMPRESENTATION_INTEGRATION'); ?></dt>
                            <dd id="jempresentation-integration-meta" class="col-sm-8"></dd>

                            <dt class="col-sm-4"><?php echo Text::_('COM_JEMPRESENTATION_BRIDGE'); ?></dt>
                            <dd id="jempresentation-bridge-meta" class="col-sm-8"></dd>
                        </dl>

                        <div id="jempresentation-runtime-warning" class="alert alert-warning d-none mb-3" role="status"></div>

                        <div class="jempresentation-preview" aria-label="<?php echo $this->escape(Text::_('COM_JEMPRESENTATION_SCHEMATIC_PREVIEW')); ?>">
                            <div class="jempresentation-preview-label"><?php echo Text::_('COM_JEMPRESENTATION_SCHEMATIC_PREVIEW'); ?></div>

                            <div data-jempresentation-preview="standard" class="jempresentation-preview-canvas d-none">
                                <div class="jp-block jp-title"><?php echo Text::_('COM_JEMPRESENTATION_PREVIEW_EVENT_TITLE'); ?></div>
                                <div class="jp-block jp-content"><?php echo Text::_('COM_JEMPRESENTATION_PREVIEW_JEM_DATA'); ?></div>
                                <div class="jp-block jp-media"><?php echo Text::_('COM_JEMPRESENTATION_PREVIEW_IMAGE_BY_JEM'); ?></div>
                            </div>

                            <div data-jempresentation-preview="hero" class="jempresentation-preview-canvas d-none">
                                <div class="jp-block jp-title jp-center"><?php echo Text::_('COM_JEMPRESENTATION_PREVIEW_EVENT_TITLE'); ?></div>
                                <div class="jp-block jp-hero jp-center"><?php echo Text::_('COM_JEMPRESENTATION_PREVIEW_HERO_ARTWORK'); ?></div>
                                <div class="jp-block jp-content"><?php echo Text::_('COM_JEMPRESENTATION_PREVIEW_EVENT_DATA'); ?></div>
                            </div>

                            <div data-jempresentation-preview="two-column" class="jempresentation-preview-canvas jp-two-column d-none">
                                <div class="jp-block jp-content"><?php echo Text::_('COM_JEMPRESENTATION_PREVIEW_EVENT_DATA'); ?></div>
                                <div class="jp-block jp-media"><?php echo Text::_('COM_JEMPRESENTATION_PREVIEW_ARTWORK'); ?></div>
                            </div>

                            <div data-jempresentation-preview="route" class="jempresentation-preview-canvas d-none">
                                <div class="jp-block jp-title"><?php echo Text::_('COM_JEMPRESENTATION_PREVIEW_EVENT_TITLE'); ?></div>
                                <div class="jp-block jp-hero jp-center"><?php echo Text::_('COM_JEMPRESENTATION_PREVIEW_ROUTE_PLACEHOLDER'); ?></div>
                                <div class="jp-block jp-content"><?php echo Text::_('COM_JEMPRESENTATION_PREVIEW_PLANNED'); ?></div>
                            </div>

                            <div data-jempresentation-preview="unknown" class="jempresentation-preview-canvas d-none">
                                <div class="jp-block jp-content"><?php echo Text::_('COM_JEMPRESENTATION_PREVIEW_UNKNOWN'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0"><?php echo Text::_('COM_JEMPRESENTATION_INTEGRATION_STATUS'); ?></h2>
                </div>
                <div class="card-body">
                    <dl class="jempresentation-status-list mb-0">
                        <div data-jempresentation-status="runtime" data-state="<?php echo $this->escape((string) ($this->integrationStatus['runtime']['state'] ?? 'unknown')); ?>">
                            <dt><?php echo Text::_('COM_JEMPRESENTATION_RUNTIME_PLUGIN'); ?></dt>
                            <dd>
                                <span class="badge text-bg-<?php echo $statusClass((string) ($this->integrationStatus['runtime']['state'] ?? 'unknown')); ?>">
                                    <?php echo $this->escape((string) ($this->integrationStatus['runtime']['label'] ?? Text::_('COM_JEMPRESENTATION_STATUS_UNKNOWN'))); ?>
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt><?php echo Text::_('COM_JEMPRESENTATION_DEFAULT_SITE_TEMPLATE'); ?></dt>
                            <dd><?php echo $this->escape((string) ($this->integrationStatus['template']['label'] ?? Text::_('COM_JEMPRESENTATION_STATUS_UNKNOWN'))); ?></dd>
                        </div>
                        <div data-jempresentation-status="bridge" data-state="<?php echo $this->escape((string) ($this->integrationStatus['bridge']['state'] ?? 'unknown')); ?>" data-role="<?php echo $this->escape((string) ($this->integrationStatus['bridge']['role'] ?? 'unknown')); ?>">
                            <dt><?php echo Text::_('COM_JEMPRESENTATION_HERO_BRIDGE'); ?></dt>
                            <dd>
                                <span class="badge text-bg-<?php echo $statusClass((string) ($this->integrationStatus['bridge']['state'] ?? 'unknown')); ?>">
                                    <?php echo $this->escape((string) ($this->integrationStatus['bridge']['label'] ?? Text::_('COM_JEMPRESENTATION_STATUS_UNKNOWN'))); ?>
                                </span>
                                <?php if (!empty($this->integrationStatus['bridge']['help'])) : ?>
                                    <div class="small text-muted mt-1"><?php echo $this->escape((string) $this->integrationStatus['bridge']['help']); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($this->integrationStatus['bridge']['files'])) : ?>
                                    <ul class="list-unstyled small mt-2 mb-0 jempresentation-bridge-files">
                                        <?php foreach ($this->integrationStatus['bridge']['files'] as $bridgeFile) : ?>
                                            <li>
                                                <code><?php echo $this->escape((string) ($bridgeFile['path'] ?? '')); ?></code>
                                                <span class="ms-2 text-muted"><?php echo $this->escape((string) ($bridgeFile['label'] ?? '')); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </dd>
                        </div>
                        <div data-jempresentation-status="native-api" data-state="<?php echo $this->escape((string) ($this->integrationStatus['native_api']['state'] ?? 'unknown')); ?>" data-role="<?php echo $this->escape((string) ($this->integrationStatus['native_api']['role'] ?? 'unknown')); ?>">
                            <dt><?php echo Text::_('COM_JEMPRESENTATION_NATIVE_JEM_API'); ?></dt>
                            <dd>
                                <span class="badge text-bg-<?php echo $statusClass((string) ($this->integrationStatus['native_api']['state'] ?? 'unknown')); ?>">
                                    <?php echo $this->escape((string) ($this->integrationStatus['native_api']['label'] ?? Text::_('COM_JEMPRESENTATION_STATUS_UNKNOWN'))); ?>
                                </span>
                                <?php if (!empty($this->integrationStatus['native_api']['help'])) : ?>
                                    <div class="small text-muted mt-1"><?php echo $this->escape((string) $this->integrationStatus['native_api']['help']); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($this->integrationStatus['native_api']['source'])) : ?>
                                    <div class="small mt-2"><code><?php echo $this->escape((string) $this->integrationStatus['native_api']['source']); ?></code></div>
                                <?php endif; ?>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="alert alert-info mt-3 mb-0">
                <?php echo Text::_('COM_JEMPRESENTATION_NON_ASSIGNED_UNCHANGED'); ?>
            </div>
        </div>
    </div>

    <script type="application/json" id="jempresentation-registry-data"><?php echo $registryJson ?: '{}'; ?></script>
    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
