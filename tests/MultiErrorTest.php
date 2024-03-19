<?php
declare(strict_types=1);

namespace Opis\JsonSchema\Test;

use Opis\JsonSchema\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Tests that multiple errors on the same level are possible.
 */
final class MultiErrorTest extends TestCase
{

    public function test(): void {
        $validator = new Validator(NULL, 20);
        $data = (object) [
            'a' => 1,
        ];

        $schema = file_get_contents(__DIR__ . '/schemas/multi-error.json');
        $result = $validator->validate($data, $schema);
        static::assertFalse($result->isValid());

        $rootError = $result->error();
        static::assertNotNull($rootError);
        static::assertSame('schema', $rootError->keyword());
        static::assertSame('The data must match schema: {data}', $rootError->message());
        static::assertSame($data, $rootError->args()['data']);
        static::assertSame([], $rootError->data()->fullPath());

        $subErrors = $rootError->subErrors();
        static::assertCount(2, $subErrors);
        static::assertSame('required', $subErrors[0]->keyword());
        static::assertSame('properties', $subErrors[1]->keyword());
    }

}
