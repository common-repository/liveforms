var registerBlockType = wp.blocks.registerBlockType;
var InspectorControls = wp.editor.InspectorControls;
var _wp$components = wp.components,
    TextControl = _wp$components.TextControl,
    SelectControl = _wp$components.SelectControl,
    ServerSideRender = wp.serverSideRender,
    Modal = _wp$components.Modal,
    Button = _wp$components.Button,
    PanelBody = _wp$components.PanelBody;
var __ = wp.i18n.__;

function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

var _lficon = function (props) {
    return /*#__PURE__*/React.createElement("svg", _extends({
        xmlns: "http://www.w3.org/2000/svg",
        xmlnsXlink: "http://www.w3.org/1999/xlink",
        width: 200,
        height: 200,
        viewBox: "0 0 1000 1000"
    }, props), /*#__PURE__*/React.createElement("image", {
        x: 85,
        y: 45,
        width: 830,
        height: 910,
        xlinkHref: "data:img/png;base64,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"
    }), /*#__PURE__*/React.createElement("image", {
        x: 292,
        y: 604,
        width: 413,
        height: 76,
        xlinkHref: "data:img/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFMAAAAPCAYAAACY/vOMAAAA10lEQVRYhe3WLU4DQRzG4acfKQkg0ByAGlwVx0BwAEjwXACBIiCweDyhjkB7gcoGi8NwBALlI3+ylSBgkm1n51ErNtm8PzGzra37T5V1HGCven5R/KSHd9ziEs/x3jzmNiZYLfn+ZBfDNlZwV0L+yw36EfMIm0s8ZFGcRMz9pldIZCdibmQxpX5rEXPW9AqJzNpZzFgQJWZCEbOTzZp6dSLmW5MLJPR9Zl5nM6deDxHzAq9NrpDIecR8wmEWc+pzinG3+vwVpjjDAB/l//NXcWlHu0ccYwRfcocek2ngV/0AAAAASUVORK5CYII="
    }), /*#__PURE__*/React.createElement("image", {
        "data-name": "Text Bar",
        x: 422,
        y: 632,
        width: 151,
        height: 22,
        xlinkHref: "data:img/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAECAYAAACZfY9fAAAAXElEQVQYlb3PoQ2DQABA0XcNjiaEBAEz4MoMWAwex1YYLDOwAatgmtS3IUEQ/PUv8PJD0y4lJrzwxlecAjJsGBOsqE+qioRe61A88PwDdi89jnvMyPGJDB6TO4Yf3aUKDESxOjAAAAAASUVORK5CYII="
    }), /*#__PURE__*/React.createElement("image", {
        x: 292,
        y: 467,
        width: 413,
        height: 76,
        xlinkHref: "data:img/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFMAAAAPCAYAAACY/vOMAAAA1UlEQVRYhe3YMUrDYBTA8V9TxUHoUpwFr+Dm0qGrV3DzBkW3SjdP4eZBCh2cLD2AduvorlKJPIjgokP7Qdrk+81Z3p8HeUmnLEuVE9zgEl18yv5yUDWa4R4v8dxPzAGmOd3GrvEQMXtYor+ng+yK8wKjHDKJcWzmK84aMEzdVrGZx+1ukMxRxFw3ZJi6fRXtnj+tHDOhojo+s+11I+Z7DpnER8R8bMAgu2Aed2a/+gLqtb3Gli5iM99wtddj1O8WT7//Gp3iDkOU+f78V7y0D/GMCRbwDUhBKlRpeBDDAAAAAElFTkSuQmCC"
    }), /*#__PURE__*/React.createElement("image", {
        "data-name": "Field",
        x: 292,
        y: 329,
        width: 413,
        height: 76,
        xlinkHref: "data:img/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFMAAAAPCAYAAACY/vOMAAAA3klEQVRYhe3YMUoDQRSA4c8EIwEhSGohnYqltxCvoH0aryDY2eQAuUNIFTyCJwg2ClrY2UcwZuWFCGlEMAub3Z2v3ub9vB2G2cmyzEobl7hCFzPJb/bwgRGGeI/vfmKe4AGdlO9fLjCJmLt4xmEJh9gWCxw1cJ1Cbiw63sZmPuK45MNsg9coelD3CjnZj5jzSoxSvHmj7gXylGLmKGI2KzNNsZoR87POBXK0PDPHlRmnWNO4Z/bwlH73jZ3HZr6gX/JBijbA/fqr0RnucLo6R79qm+ZvsYQtvOEmHjngG/quLIfGOY94AAAAAElFTkSuQmCC"
    }));
};

registerBlockType('liveforms/form', {
    title: 'Live Forms',

    description: 'Embed Live Form',

    keywords: [__('form builder contact survey job application customer support drag drop'), 'form'],

    icon: _lficon,

    category: 'embed',

    attributes: {
        form_id: {
            type: 'string'
        }
    },

    edit: function edit(_ref) {
        var attributes = _ref.attributes,
            className = _ref.className,
            setAttributes = _ref.setAttributes;

        return [React.createElement(
            InspectorControls,
            null,
            React.createElement(
                'div',
                { className: 'w3eden' },
                React.createElement(
                    PanelBody,
                    null,
                    React.createElement(SelectControl, {
                        label: "Select Form:",
                        class: "form-control custom-select",
                        value: attributes.form_id,
                        options: __lf_allforms,
                        onChange: (form_id) => { setAttributes({form_id: form_id}) }
                    })
                )
            )
        ), React.createElement(ServerSideRender, {
            block: "liveforms/form",
            attributes: attributes
        })];
    },
    save: function save() {
        return null;
    }
});