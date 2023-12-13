<?php

function urljoin($base, $relative) {
    $parts = parse_url($relative);

    if ($parts === false) {
        return false;
    }

    if (empty($parts['scheme'])) {
        $base_parts = parse_url($base);

        if ($base_parts === false) {
            return false;
        }

        $parts['scheme'] = $base_parts['scheme'];
        $parts['host'] = $base_parts['host'];

        if (empty($parts['path']) || $parts['path'][0] !== '/') {
            $basePath = explode('/', $base_parts['path']);
            array_pop($basePath);

            $parts['path'] = implode('/', $basePath) . '/' . $parts['path'];
        }
    }

    // Manually concatenate the URL components
    $result = $parts['scheme'] . '://';
    if (!empty($parts['user'])) {
        $result .= $parts['user'];
        if (!empty($parts['pass'])) {
            $result .= ':' . $parts['pass'];
        }
        $result .= '@';
    }
    $result .= $parts['host'];
    if (!empty($parts['port'])) {
        $result .= ':' . $parts['port'];
    }
    $result .= $parts['path'];
    if (!empty($parts['query'])) {
        $result .= '?' . $parts['query'];
    }
    if (!empty($parts['fragment'])) {
        $result .= '#' . $parts['fragment'];
    }

    return $result;
}

?>