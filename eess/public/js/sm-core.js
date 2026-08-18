/**
 * EESS Core JavaScript Helper Module
 */

(function() {
    'use strict';

    window.smGetAjaxUrl = function() {
        if (typeof sm_ajax_object !== 'undefined' && sm_ajax_object.ajax_url) {
            return sm_ajax_object.ajax_url;
        }
        if (typeof ajaxurl !== 'undefined') {
            return ajaxurl;
        }
        return '/wp-admin/admin-ajax.php';
    };

    window.smGetNonce = function() {
        if (typeof sm_ajax_object !== 'undefined' && sm_ajax_object.nonce) {
            return sm_ajax_object.nonce;
        }
        return '';
    };
})();
