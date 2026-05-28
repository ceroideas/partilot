<?php

namespace Tests\Unit;

use App\Support\ParticipationTicketReference;
use PHPUnit\Framework\TestCase;

class ParticipationTicketReferenceTest extends TestCase
{
    public function test_generate_produces_21_digits_with_valid_check(): void
    {
        $ref = ParticipationTicketReference::generate(1, 1);

        $this->assertSame(21, strlen($ref));
        $this->assertTrue(ctype_digit($ref));
        $this->assertTrue(ParticipationTicketReference::isValid($ref));
        $this->assertStringStartsWith('00010001', $ref);
    }

    public function test_invalid_check_is_rejected(): void
    {
        $ref = ParticipationTicketReference::generate(5, 12);
        $tampered = substr($ref, 0, 20) . ((int) substr($ref, 20, 1) + 1) % 10;

        $this->assertFalse(ParticipationTicketReference::isValid($tampered));
    }

    public function test_normalize_strips_non_digits(): void
    {
        $this->assertSame(
            '000100011234567890123',
            ParticipationTicketReference::normalize('0001 0001-1234567890123')
        );
    }

    public function test_references_in_same_set_are_not_sequential(): void
    {
        $refs = [];
        for ($i = 0; $i < 5; $i++) {
            $refs[] = ParticipationTicketReference::generate(1, 1);
        }

        $this->assertCount(5, array_unique($refs));
        for ($i = 1; $i < 5; $i++) {
            $this->assertNotEquals(
                (int) substr($refs[$i - 1], -3) + 1,
                (int) substr($refs[$i], -3),
                'Las referencias no deben diferir solo en el último dígito secuencial'
            );
        }
    }
}
