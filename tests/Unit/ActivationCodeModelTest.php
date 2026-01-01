<?php

namespace Tests\Unit;

use App\Models\ActivationCode;
use PHPUnit\Framework\TestCase;

class ActivationCodeModelTest extends TestCase
{
    public function test_fillable_contains_batch_fields()
    {
        $model = new ActivationCode();
        $fillable = $model->getFillable();

        $this->assertContains('batch_code', $fillable);
        $this->assertContains('batch_name', $fillable);
        $this->assertContains('status', $fillable);
    }
}
