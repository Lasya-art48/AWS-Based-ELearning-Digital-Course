<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mindigo\QuestionBank\Models\Question;
use Mindigo\SubjectManagement\Models\Subject;
use Mindigo\SubjectManagement\Models\SubjectTopic;
use Tests\TestCase;

class QuestionTopicLoadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_topic_endpoint_returns_only_active_topics_for_the_selected_global_subject(): void
    {
        $teacher = $this->createUser(['role' => 'teacher']);
        $owner = $this->createUser(['role' => 'teacher']);
        $subject = $this->createSubject($owner, 'Mathematics');
        $active = $this->createTopic($subject, 'Algebra');
        $this->createTopic($subject, 'Inactive topic', 'inactive');
        $deleted = $this->createTopic($subject, 'Deleted topic');
        $deleted->delete();

        $otherSubject = $this->createSubject($owner, 'Science');
        $this->createTopic($otherSubject, 'Physics');

        $this->actingAs($teacher)
            ->getJson(route('question-bank.topics', ['subject' => $subject->name]))
            ->assertOk()
            ->assertExactJson([
                'topics' => [
                    ['id' => $active->id, 'name' => 'Algebra'],
                ],
            ]);

        $this->actingAs($teacher)
            ->getJson(route('question-bank.topics', ['subject' => $otherSubject->name]))
            ->assertOk()
            ->assertExactJson([
                'topics' => [
                    ['id' => $otherSubject->topics()->where('name', 'Physics')->value('id'), 'name' => 'Physics'],
                ],
            ]);
    }

    public function test_teacher_can_save_question_with_subject_topic_relationship_and_restore_topic_for_edit(): void
    {
        $teacher = $this->createUser(['role' => 'teacher']);
        $subject = $this->createSubject($teacher, 'Mathematics');
        $topic = $this->createTopic($subject, 'Algebra');

        $response = $this->actingAs($teacher)->post(route('question-bank.store'), [
            'subject' => $subject->name,
            'topic' => $topic->name,
            'subject_topic_id' => $topic->id,
            'type' => 'single_choice',
            'difficulty' => 'medium',
            'content' => 'Solve the equation.',
            'options' => ['x = 1', 'x = 2'],
            'correct_answer_single' => 'x = 1',
        ]);

        $question = Question::query()->firstOrFail();
        $response->assertRedirect(route('question-bank.show', $question));
        $this->assertSame($subject->id, $question->subject_id);
        $this->assertSame($topic->id, $question->subject_topic_id);
        $this->assertSame($topic->name, $question->topic);

        $this->actingAs($teacher)
            ->get(route('question-bank.edit', $question))
            ->assertOk()
            ->assertSee('data-current-value="'.$topic->name.'"', false)
            ->assertSee(route('question-bank.topics'), false);
    }

    public function test_question_submission_rejects_topics_outside_the_selected_active_subject(): void
    {
        $teacher = $this->createUser(['role' => 'teacher']);
        $math = $this->createSubject($teacher, 'Mathematics');
        $science = $this->createSubject($teacher, 'Science');
        $physics = $this->createTopic($science, 'Physics');
        $algebra = $this->createTopic($math, 'Algebra', 'inactive');

        foreach ([$physics, $algebra] as $topic) {
            $response = $this->actingAs($teacher)->post(route('question-bank.store'), [
                'subject' => $math->name,
                'topic' => $topic->name,
                'subject_topic_id' => $topic->id,
                'type' => 'single_choice',
                'difficulty' => 'medium',
                'content' => 'Choose the answer.',
                'options' => ['A', 'B'],
                'correct_answer_single' => 'A',
            ]);

            $response->assertSessionHasErrors('topic');
        }

        $this->assertDatabaseCount('question_bank_questions', 0);
    }

    public function test_only_admins_can_create_or_update_subject_topics(): void
    {
        $teacher = $this->createUser(['role' => 'teacher']);
        $admin = $this->createUser(['role' => 'admin']);
        $subject = $this->createSubject($admin, 'Mathematics');
        $topicPayload = [
            'name' => 'Geometry',
            'description' => '',
            'status' => 'active',
            'sort_order' => 0,
        ];

        $this->actingAs($teacher)
            ->post(route('subjects.topics.store', $subject), $topicPayload)
            ->assertForbidden();
        $this->assertDatabaseCount('subject_topics', 0);

        $this->actingAs($admin)
            ->post(route('subjects.topics.store', $subject), $topicPayload)
            ->assertRedirect();
        $topic = SubjectTopic::query()->firstOrFail();

        $this->actingAs($admin)
            ->put(route('subjects.topics.update', $topic), [
                ...$topicPayload,
                'status' => 'inactive',
            ])
            ->assertRedirect();
        $this->assertDatabaseHas('subject_topics', [
            'id' => $topic->id,
            'status' => 'inactive',
        ]);
    }

    private function createSubject($creator, string $name): Subject
    {
        return Subject::query()->create([
            'created_by' => $creator->id,
            'name' => $name,
            'code' => strtoupper(substr($name, 0, 3)),
            'slug' => strtolower($name),
            'status' => 'active',
        ]);
    }

    private function createTopic(Subject $subject, string $name, string $status = 'active'): SubjectTopic
    {
        return $subject->topics()->create([
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)),
            'status' => $status,
        ]);
    }
}
