<?php
session_start();
session_destroy();
http_response_code(200); // Réponse HTTP OK
exit();
