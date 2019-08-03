<?php
/**
 * Returns env variable
 *
 * @param string $name
 * @param mixed $default
 * @return mixed
 */
function env ($name, $default = NULL) {
    return isset($_ENV[$name]) ? $_ENV[$name] : $default;
}
