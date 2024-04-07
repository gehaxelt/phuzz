<?php

uopz_set_return('require_login', function () {
	Users::login_user(1); // admin
    //NOP
}, true);

uopz_set_return('require_admin_login', function () {
	Users::login_user(1); // admin
    //NOP
}, true);

?>