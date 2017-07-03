/**
 * Sidebar module
 */
var sidebar = {
    sidebar: null,
    init: function () {
        // Init Slidebar
        this.sidebar = new slidebars({
            speed: 1000,
            siteClose: true,
            disableOver: false,
            hideControlClasses: false
        })
        this.sidebar.init()

        // Event listener
        let self = this;
        $('#btn-menu').click(function(e) {
            e.stopPropagation()
            self.sidebar.toggle('sidebar')
        })
        $('#container, .sb-close').click(function() {
            self.sidebar.close('sidebar');
        })

        // Submenu
        let $submenu = $('.sidebar-menu .has-submenu')
        if ($submenu.length) {
            $submenu.click(function() {
                if ($(this).hasClass('disabled')) {
                    return false;
                }

                $(this).toggleClass('active');
                let ul = $(this).find('ul');
                if ($(ul).length > 0) {
                    if ($(this).hasClass('active')) {
                        $(ul).slideDown(600);
                    } else {
                        $(ul).slideUp(400);
                    }
                }
                return false;
            });
        }
    }
}

module.exports = sidebar;
