<!-- TEMPLATE SETTINGS -->
<div class="fixed-panel" id="fixed_panel">
    <div class="fixed-panel__header">
        <h5 class="title">Template settings</h5>
        <button class="btn btn-light btn-icon" data-action="fixedpanel-toggle"><span class="li-cross"></span></button>
    </div>
    <div class="fixed-panel__content scroll" style="max-height: 100%">
        <h5>Information</h5>
        <p class="margin-bottom-20">
            Use this panel to configure template settings and layout options.
        </p>
        <div class="form-group">
            <div class="custom-control custom-checkbox margin-bottom-30">
                <input type="checkbox" class="custom-control-input" id="rw_settings_show"><label class="custom-control-label" for="rw_settings_show">Disable auto show template settings</label>
            </div>
        </div>
        <div class="divider divider--sm">
        </div>
        <h5 class="margin-top-30">Layout option</h5>
        <div class="form-group">
            <select class="custom-select margin-bottom-20" id="rw_settings_layout">
                <option value="default">Default</option>
                <option value="boxed">Boxed</option>
                <option value="indent">Indent</option>
            </select>
        </div>
        <div class="d-none" id="rw_settings_layout_boxed_group">
            <div class="custom-control custom-checkbox margin-bottom-10">
                <input type="checkbox" class="custom-control-input" id="rw_settings_layout_boxed_vspace"><label class="custom-control-label" for="rw_settings_layout_boxed_vspace">Vertical spacing</label>
            </div>
            <div class="custom-control custom-checkbox margin-bottom-10">
                <input type="checkbox" class="custom-control-input" id="rw_settings_layout_boxed_rounded"><label class="custom-control-label" for="rw_settings_layout_boxed_rounded">Rounded corners</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="rw_settings_layout_boxed_shadowed"><label class="custom-control-label" for="rw_settings_layout_boxed_shadowed">Add shadows</label>
            </div>
        </div>
        <div class="d-none" id="rw_settings_layout_indent_group">
            <div class="d-none" id="rw_settings_layout_indent_header_group">
                <div class="custom-control custom-checkbox margin-bottom-10">
                    <input type="checkbox" class="custom-control-input" id="rw_settings_layout_indent_header"><label class="custom-control-label" for="rw_settings_layout_indent_header">Single header</label>
                </div>
                <div class="custom-control custom-checkbox margin-bottom-10">
                    <input type="checkbox" class="custom-control-input" id="rw_settings_layout_indent_header_relative"><label class="custom-control-label" for="rw_settings_layout_indent_header_relative">Relative header</label>
                </div>
            </div>
            <div class="d-none" id="rw_settings_layout_indent_container">
                <div class="custom-control custom-checkbox margin-bottom-10">
                    <input type="checkbox" class="custom-control-input" id="rw_settings_layout_indent_container_single"><label class="custom-control-label" for="rw_settings_layout_indent_container_single">Single page container</label>
                </div>
            </div>
            <div class="custom-control custom-checkbox margin-bottom-10">
                <input type="checkbox" class="custom-control-input" id="rw_settings_layout_indent_rounded"><label class="custom-control-label" for="rw_settings_layout_indent_rounded">Rounded corners</label>
            </div>
            <div class="custom-control custom-checkbox margin-bottom-10">
                <input type="checkbox" class="custom-control-input" id="rw_settings_layout_indent_shadowed"><label class="custom-control-label" for="rw_settings_layout_indent_shadowed">Add shadows</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="rw_settings_layout_indent_move_heading"><label class="custom-control-label" for="rw_settings_layout_indent_move_heading">Heading under header (relative header)</label>
            </div>
        </div>
        <div class="d-none" id="rw_settings_layout_bgs_group">
            <h5 class="margin-top-30">Backgrounds</h5>
            <div class="bg-examples margin-bottom-20" id="rw_settings_layout_bgs">
                <div class="bg-gradient-1" data-toggle="tooltip" data-placement="top" title="bg-gradient-1">
                </div>
                <div class="bg-gradient-2" data-toggle="tooltip" data-placement="top" title="bg-gradient-2">
                </div>
                <div class="bg-gradient-3" data-toggle="tooltip" data-placement="top" title="bg-gradient-3">
                </div>
                <div class="bg-gradient-4" data-toggle="tooltip" data-placement="top" title="bg-gradient-4">
                </div>
                <div class="bg-gradient-5" data-toggle="tooltip" data-placement="top" title="bg-gradient-5">
                </div>
                <div class="bg-gradient-6" data-toggle="tooltip" data-placement="top" title="bg-gradient-6">
                </div>
                <div class="bg-gradient-7" data-toggle="tooltip" data-placement="top" title="bg-gradient-7">
                </div>
                <div class="bg-gradient-8" data-toggle="tooltip" data-placement="top" title="bg-gradient-8">
                </div>
                <div class="bg-gradient-9" data-toggle="tooltip" data-placement="top" title="bg-gradient-9">
                </div>
                <div class="bg-gradient-10" data-toggle="tooltip" data-placement="top" title="bg-gradient-10">
                </div>
            </div>
        </div>
        <div class="d-none" id="rw_settings_header_opt_group">
            <div class="divider divider--sm">
            </div>
            <h5 class="margin-top-30">Header options</h5>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="rw_settings_header_fixed"><label class="custom-control-label" for="rw_settings_header_fixed">Fixed header</label>
            </div>
            <div class="custom-control custom-checkbox margin-bottom-20">
                <input type="checkbox" class="custom-control-input" id="rw_settings_header_invert"><label class="custom-control-label" for="rw_settings_header_invert">Invert style</label>
            </div>
        </div>
        <div class="d-none" id="rw_settings_container_opt_group">
            <div class="divider divider--sm">
            </div>
            <h5 class="margin-top-30">Container</h5>
            <div class="custom-control custom-checkbox margin-bottom-20">
                <input type="checkbox" class="custom-control-input" id="rw_settings_container_invert"><label class="custom-control-label" for="rw_settings_container_invert">Invert style</label>
            </div>
        </div>
        <div class="d-none" id="rw_settings_navigation_opt_group">
            <div class="divider divider--sm">
            </div>
            <h5 class="margin-top-30">Navigation options</h5>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="rw_settings_nav_minimized"><label class="custom-control-label" for="rw_settings_nav_minimized">Minimized navigation</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="rw_settings_nav_hidden"><label class="custom-control-label" for="rw_settings_nav_hidden">Hidden navigation</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="rw_settings_nav_fixed" disabled="disabled"><label class="custom-control-label" for="rw_settings_nav_fixed">Fixed navigation</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="rw_settings_nav_invert"><label class="custom-control-label" for="rw_settings_nav_invert">Invert style</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="rw_settings_nav_vmiddle"><label class="custom-control-label" for="rw_settings_nav_vmiddle">Vertical middle position</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="rw_settings_nav_condensed"><label class="custom-control-label" for="rw_settings_nav_condensed">Condensed navigation</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="rw_settings_nav_custom"><label class="custom-control-label" for="rw_settings_nav_custom">Custom navigation</label>
            </div>
            <div class="custom-control custom-checkbox margin-bottom-20">
                <input type="checkbox" class="custom-control-input" id="rw_settings_nav_cpanel"><label class="custom-control-label" for="rw_settings_nav_cpanel">Remove control panel</label>
            </div>
        </div>
        <div class="d-none" id="rw_settings_sidepanel_opt_group">
            <div class="divider divider--sm">
            </div>
            <h5 class="margin-top-30">Sidepanel options</h5>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="rw_settings_sidepanel_hidden"><label class="custom-control-label" for="rw_settings_sidepanel_hidden">Hide sidepanel</label>
            </div>
            <div class="custom-control custom-checkbox margin-bottom-20">
                <input type="checkbox" class="custom-control-input" id="rw_settings_sidepanel_invert"><label class="custom-control-label" for="rw_settings_sidepanel_invert">Invert style</label>
            </div>
        </div>
        <div class="divider divider--sm">
        </div>
        <h5 class="margin-top-30">Content options</h5>
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="rw_settings_content_fluid"><label class="custom-control-label" for="rw_settings_content_fluid">Fluid container</label>
        </div>
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="rw_settings_content_invert"><label class="custom-control-label" for="rw_settings_content_invert">Invert content</label>
        </div>
    </div>
</div>
<!-- //END TEMPLATE SETTINGS -->