<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mindigo\ExamManagement\Services\ExamInventoryService;
use Mindigo\ExamManagement\Services\ExamService;
use Mindigo\QuestionBank\Models\Question;
use Mindigo\SubjectManagement\Models\Subject;
use Tests\TestCase;

class ExamFoundationPhaseZeroOneTest extends TestCase
{
    use RefreshDatabase;

    public function test_exam_inventory_command_reports_existing_schema(): void
    {
        $report = app(ExamInventoryService::class)->collect();

        $this->assertSame('exam', $report['schema']);
        $this->assertSame(0, $report['tables']['exam_attempts']);
        $this->assertSame(0, $report['orphaned_attempts']);

        $this->artisan('exam:inventory', ['--json' => true])->assertSuccessful();
    }

    public function test_teacher_can_access_the_exam_foundation(): void
    {
        $teacher = $this->createUser(['role' => 'teacher']);

        $this->actingAs($teacher)->get(route('teacher.exams.foundation'))
            ->assertOk()
            ->assertSee(__('Mindigo-exam-management::app.foundation.foundation_title'))
            ->assertDontSee('reviewing', false);
    }

    public function test_admin_and_student_cannot_access_the_teaching_foundation(): void
    {
        $this->actingAs($this->createUser(['role' => 'admin']))
            ->get(route('teacher.exams.foundation'))
            ->assertRedirect();

        $this->actingAs($this->createUser(['role' => 'student']))
            ->get(route('teacher.exams.foundation'))
            ->assertRedirect();
    }

    public function test_exam_topic_options_include_only_active_topics_for_active_subjects(): void
    {
        $subject = Subject::query()->create([
            'name' => 'Exam Topic Subject',
            'code' => 'EXAM-TOPIC',
            'slug' => 'exam-topic-subject',
            'status' => 'active',
        ]);
        $subject->topics()->create([
            'name' => 'Active topic',
            'slug' => 'active-topic',
            'status' => 'active',
        ]);
        $subject->topics()->create([
            'name' => 'Inactive topic',
            'slug' => 'inactive-topic',
            'status' => 'inactive',
        ]);

        $topicsBySubject = app(ExamService::class)->topicsBySubject();

        $this->assertSame(['Active topic'], $topicsBySubject['Exam Topic Subject']);
    }

    public function test_question_preview_reports_available_and_selected_counts_truthfully(): void
    {
        $teacher = $this->createUser(['role' => 'teacher']);
        $service = app(ExamService::class);
        $input = ['counts' => array_fill_keys(Question::TYPES, 0)];
        $input['counts']['single_choice'] = 20;

        $empty = $service->previewQuestions($input, $teacher);
        $this->assertSame(0, $empty['generated_count']);
        $this->assertSame(20, $empty['requested_count']);
        $this->assertSame(0, $empty['available']['single_choice']);

        Question::factory()->count(5)->create(['status' => 'approved']);
        $partial = $service->previewQuestions($input, $teacher);
        $this->assertSame(5, $partial['generated_count']);
        $this->assertSame(5, $partial['available']['single_choice']);

        Question::factory()->count(15)->create(['status' => 'approved']);
        $exact = $service->previewQuestions($input, $teacher);
        $this->assertSame(20, $exact['generated_count']);
        $this->assertCount(20, $exact['questions']['single_choice']);

        Question::factory()->count(5)->create(['status' => 'approved']);
        $moreThanRequested = $service->previewQuestions($input, $teacher);
        $this->assertSame(25, $moreThanRequested['available']['single_choice']);
        $this->assertSame(20, $moreThanRequested['generated_count']);
    }

    public function test_admin_can_monitor_exam_list_but_cannot_enter_authoring_routes(): void
    {
        $admin = $this->createUser(['role' => 'admin']);

        $this->actingAs($admin)->get(route('teacher.exams.index'))->assertRedirect();
        $this->actingAs($admin)->get(route('exams.index'))->assertOk();
        $this->actingAs($admin)->get(route('exams.create'))->assertForbidden();
    }
}
