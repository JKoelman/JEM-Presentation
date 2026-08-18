(() => {
    'use strict';
    const init = () => {
        const registryNode = document.getElementById('jempresentation-registry-data'); const profileSelect = document.getElementById('jform_profile'); const layoutSelect = document.getElementById('jform_layout');
        if (!registryNode || !profileSelect || !layoutSelect) return;
        let registry = {}; try { registry = JSON.parse(registryNode.textContent || '{}'); } catch (error) { return; }
        const setText = (id, value) => { const node = document.getElementById(id); if (node) node.textContent = value || ''; };
        const showPreview = (previewId) => document.querySelectorAll('[data-jempresentation-preview]').forEach((node) => node.classList.toggle('d-none', node.dataset.jempresentationPreview !== previewId));
        const combinationSupported = (profile, layoutId) => profile.status === 'available' && Array.isArray(profile.supportedLayouts) && profile.supportedLayouts.includes(layoutId) && registry.layouts?.[layoutId]?.selectable === true;
        const update = () => {
            const profile = registry.profiles?.[profileSelect.value] || {label: profileSelect.value || '-',status: 'unknown',statusLabel: registry.labels?.status || 'Unknown',supportedLayouts: []};
            const layout = registry.layouts?.[layoutSelect.value] || {label: layoutSelect.value || '-',description: '',integrationLabel: '-',bridgeLabel: '-',status: 'unknown',statusLabel: '-',preview: 'unknown',selectable: false};
            setText('jempresentation-layout-title', layout.label); setText('jempresentation-layout-description', layout.description); setText('jempresentation-profile-meta', `${profile.label} — ${profile.statusLabel}`); setText('jempresentation-integration-meta', layout.integrationLabel); setText('jempresentation-bridge-meta', layout.bridgeLabel); setText('jempresentation-layout-status', layout.statusLabel);
            const statusBadge = document.getElementById('jempresentation-layout-status'); if (statusBadge) statusBadge.className = 'badge ' + (layout.status === 'confirmed' ? 'text-bg-success' : layout.status === 'poc' ? 'text-bg-warning' : 'text-bg-secondary');
            const warning = document.getElementById('jempresentation-runtime-warning'); if (warning) { const unsupported = !combinationSupported(profile, layoutSelect.value); warning.classList.toggle('d-none', !unsupported); warning.textContent = unsupported ? (registry.labels?.unsupported || '') : ''; }
            showPreview(layout.preview || 'unknown');
        };
        profileSelect.addEventListener('change', update); layoutSelect.addEventListener('change', update); update();
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, {once: true}); else init();
})();
