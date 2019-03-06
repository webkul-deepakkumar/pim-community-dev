'use strict';

define(['jquery', 'backbone', 'underscore', 'pim/router', 'oro/translator', 'oro/app', 'oro/mediator', 'oro/layout',
        'pim/dialog', 'oro/messenger', 'bootstrap', 'jquery-setup'
], function ($, Backbone, _, router, __, app, mediator, layout, Dialog, messenger) {


    /* ============================================================
     * from layout.js
     * ============================================================ */
    return function () {
        mediator.once('tab:changed', function () {
            setTimeout(function () {
                // emulates 'document ready state' for selenium tests
                document['page-rendered'] = true;
                mediator.trigger('page-rendered');
            }, 50);
        });
        layout.init();

        /* ============================================================
         * from form_buttons.js
         * ============================================================ */
        $(document).on('click', '.action-button', function () {
            var actionInput = $('input[name = "input_action"]');
            actionInput.val($(this).attr('data-action'));
            $('#' + actionInput.attr('data-form-id')).submit();
        });

        /* ============================================================
         * from remove.confirm.js
         * ============================================================ */

        $(document).on('click', '.remove-button', function () {
            var el = $(this);
            var message = el.data('message');
            const subTitle = el.data('subtitle');

            const doDelete = function () {
                router.showLoadingMask();

                $.ajax({
                    url: el.data('url'),
                    type: 'DELETE',
                    success: function () {
                        el.trigger('removesuccess');
                        messenger.enqueueMessage(
                            'success',
                            el.data('success-message'),
                            { 'hashNavEnabled': true }
                        );
                        if (el.data('redirect')) {
                            $.isActive(true);
                            Backbone.history.navigate('#' + el.data('redirect'));
                        } else {
                            router.hideLoadingMask();
                        }
                    },
                    error: function () {
                        router.hideLoadingMask();

                        messenger.notify(
                            'error',
                            el.data('error-message') ||
                                __('Unexpected error occurred. Please contact system administrator.'),
                            { flash: false }
                        );
                    }
                });
            };

            this.confirmModal = Dialog.confirmDelete(
                message,
                __('pim_common.confirm_deletion'),
                doDelete,
                subTitle
            );

            return false;
        });
    };
});
