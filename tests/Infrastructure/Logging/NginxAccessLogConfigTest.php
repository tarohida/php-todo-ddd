<?php
declare(strict_types=1);

namespace Tests\Infrastructure\Logging;

use PHPUnit\Framework\TestCase;

final class NginxAccessLogConfigTest extends TestCase
{
    public function test_access_log_is_json_and_contains_only_query_safe_whitelisted_fields(): void
    {
        $config = file_get_contents(dirname(__DIR__, 3) . '/.docker/conf/nginx/default.conf');
        self::assertIsString($config);
        self::assertMatchesRegularExpression(
            '/log_format api_json escape=json .*?;/s',
            $config,
        );
        preg_match('/log_format api_json escape=json (.*?);/s', $config, $matches);
        $format = preg_replace('/\s+/', ' ', trim($matches[1]));
        self::assertSame(
            '\'{"method":"$request_method","path":"$uri","status":$status,'
            . '"duration_seconds":$request_time,"request_id":"$sent_http_x_request_id"}\'',
            $format,
        );
        preg_match_all('/\$[A-Za-z0-9_]+/', $format, $variables);
        self::assertSame([
            '$request_method',
            '$uri',
            '$status',
            '$request_time',
            '$sent_http_x_request_id',
        ], $variables[0]);
        self::assertStringContainsString(
            'access_log /dev/stdout api_json;',
            $config,
        );
        self::assertStringNotContainsString('/var/log/nginx/project_access.log', $config);
        self::assertSame(1, preg_match_all('/^\s*error_log\s+/m', $config));
        self::assertStringContainsString('error_log /dev/null crit;', $config);
        self::assertStringNotContainsString('project_error.log', $config);
    }
}
