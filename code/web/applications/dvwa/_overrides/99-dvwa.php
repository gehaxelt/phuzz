<?php

##########################################################################################
#                               DVWA auth function overrides                               #
##########################################################################################

uopz_set_return('dvwaIsLoggedIn', function () {
    $dvwaSession =& dvwaSessionGrab();
    $dvwaSession['username'] = 'admin';
    return true;
}, true);

?>