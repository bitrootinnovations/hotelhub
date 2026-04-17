<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * PHP does not populate $_FILES or $_POST for PUT/PATCH multipart/form-data requests.
 * This middleware manually parses the raw input and merges fields + files into the
 * Laravel Request object — so PUT file uploads from React Native work without any
 * changes to the mobile app.
 */
class ParseMultipartPut
{
    public function handle(Request $request, Closure $next)
    {
        if (
            in_array($request->method(), ['PUT', 'PATCH']) &&
            str_contains($request->header('Content-Type', ''), 'multipart/form-data')
        ) {
            $rawInput = file_get_contents('php://input');

            preg_match('/boundary=([^\s;]+)/', $request->header('Content-Type', ''), $matches);
            if (empty($matches[1])) {
                return $next($request);
            }

            $boundary = $matches[1];
            $parts    = array_slice(explode('--' . $boundary, $rawInput), 1);

            $postData = [];
            $fileData = [];

            foreach ($parts as $part) {
                if (str_starts_with($part, '--')) break;

                $part = ltrim($part, "\r\n");
                if (!str_contains($part, "\r\n\r\n")) continue;

                [$rawHeaders, $body] = explode("\r\n\r\n", $part, 2);
                $body = substr($body, 0, -2); // strip trailing \r\n

                $headers = [];
                foreach (explode("\r\n", $rawHeaders) as $header) {
                    if (!str_contains($header, ':')) continue;
                    [$name, $value]           = explode(':', $header, 2);
                    $headers[strtolower(trim($name))] = trim($value);
                }

                $disposition = $headers['content-disposition'] ?? '';
                preg_match('/name="([^"]+)"/',     $disposition, $nameMatch);
                preg_match('/filename="([^"]+)"/', $disposition, $filenameMatch);

                if (empty($nameMatch[1])) continue;

                $fieldName = $nameMatch[1];

                if (!empty($filenameMatch[1])) {
                    $tmpFile = tempnam(sys_get_temp_dir(), 'rn_upload_');
                    file_put_contents($tmpFile, $body);

                    $fileData[$fieldName] = new UploadedFile(
                        $tmpFile,
                        $filenameMatch[1],
                        $headers['content-type'] ?? 'application/octet-stream',
                        null,
                        true  // test mode — skip is_uploaded_file() check
                    );
                } else {
                    $postData[$fieldName] = $body;
                }
            }

            if (!empty($postData)) {
                $request->merge($postData);
            }
            if (!empty($fileData)) {
                $request->files->add($fileData);
            }
        }

        return $next($request);
    }
}
