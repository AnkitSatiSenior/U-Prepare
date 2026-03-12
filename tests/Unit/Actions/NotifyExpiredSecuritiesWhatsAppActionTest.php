<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use App\Actions\NotifyExpiredSecuritiesWhatsAppAction;
use App\Models\ContractSecurity;
use App\Models\Contract;
use App\Models\Project;
use App\Models\Assignment;
use App\Models\User;
use App\Models\WhatsAppLog;
use App\Jobs\SendExpiredSecurityWhatsAppJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

class NotifyExpiredSecuritiesWhatsAppActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake(); // Prevent actual jobs from firing during unit tests
    }

    public function test_it_dispatches_jobs_for_expired_securities_with_valid_phones()
    {
        // 1. Arrange: Create mock data
        $user = User::factory()->create(['phone' => '918868945220', 'name' => 'John Doe']);
        
        $project = Project::factory()->create(['name' => 'Test Project']);
        Assignment::factory()->create(['project_id' => $project->id, 'user_id' => $user->id]);
        
        $contract = Contract::factory()->create(['project_id' => $project->id]);
        
        // This security IS expired
        $security = ContractSecurity::factory()->create([
            'contract_id' => $contract->id,
            'issued_end_date' => now()->subDays(2), 
        ]);

        // 2. Act: Execute the action
        $action = new NotifyExpiredSecuritiesWhatsAppAction();
        $action->execute();

        // 3. Assert: Verify the job was pushed to the queue exactly once
        Queue::assertPushed(SendExpiredSecurityWhatsAppJob::class, function ($job) use ($security, $user) {
            return $job->security->id === $security->id && 
                   $job->user->id === $user->id &&
                   str_contains($job->message, 'Alert: Hello John Doe');
        });
    }

    public function test_it_skips_users_without_phone_numbers()
    {
        // 1. Arrange: User has NO phone number
        $user = User::factory()->create(['phone' => null]);
        
        $project = Project::factory()->create();
        Assignment::factory()->create(['project_id' => $project->id, 'user_id' => $user->id]);
        $contract = Contract::factory()->create(['project_id' => $project->id]);
        
        ContractSecurity::factory()->create([
            'contract_id' => $contract->id,
            'issued_end_date' => now()->subDays(2),
        ]);

        // 2. Act
        $action = new NotifyExpiredSecuritiesWhatsAppAction();
        $action->execute();

        // 3. Assert: Job should NOT be pushed
        Queue::assertNothingPushed();
    }
}