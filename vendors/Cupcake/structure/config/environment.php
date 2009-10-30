<?php
#
# CUPCAKE_ENV must be set. It will also load the appropiate environment file in environments/
#
define("CUPCAKE_ENV", "development");
date_default_timezone_set("America/Los_Angeles");

CupcakeConfig::set("session_key", "_@app_name@_session");
CupcakeConfig::set("secret", "@secret@");

?>