<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mindigo\QuestionBank\Models\Question;
use Mindigo\SubjectManagement\Models\Subject;
use Tests\TestCase;

class QuestionAnswerSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_selected_answers_are_saved_for_each_supported_answer_type(): void
    {
        $teacher = $this->createUser(['role' => 'teacher']);
        $subject = $this->createSubject($teacher);
        $submissions = [
            [
                'type' => 'single_choice',
                'options' => ['A', 'B'],
                'correct_answer_single' => 'B',
                'expected' => ['B'],
            ],
            [
                'type' => 'multiple_choice',
                'options' => ['A', 'B', 'C'],
                'correct_answers' => ['A', 'C'],
                'expected' => ['A', 'C'],
            ],
            [
                'type' => 'true_false',
                'options' => ['True', 'False'],
                'correct_answer_single' => 'True',
                'expected' => ['True'],
            ],
            [
                'type' => 'short_answer',
                'options' => [],
                'short_answer_text' => "42\nforty-two",
                'expected' => ['42', 'forty-two'],
            ],
        ];

        foreach ($submissions as $submission) {
            $response = $this->actingAs($teacher)->post(route('question-bank.store'), [
                'subject' => $subject->name,
                'type' => $submission['type'],
                'difficulty' => 'medium',
                'content' => 'Question for '.$submission['type'],
                'options' => $submission['options'],
                'correct_answer_single' => $submission['correct_answer_single'] ?? '',
                'correct_answers' => $submission['correct_answers'] ?? [],
                'short_answer_text' => $submission['short_answer_text'] ?? '',
            ]);

            $question = Question::query()->where('content', 'Question for '.$submission['type'])->firstOrFail();
            $response->assertRedirect(route('question-bank.show', $question));
            $this->assertSame($submission['expected'], $question->correct_answers);
        }
    }

    public function test_question_submission_requires_an_answer_for_all_answerable_types(): void
    {
        $teacher = $this->createUser(['role' => 'teacher']);
        $subject = $this->createSubject($teacher);

        foreach (['single_choice', 'multiple_choice', 'true_false', 'short_answer'] as $type) {
            $this->actingAs($teacher)
                ->post(route('question-bank.store'), [
                    'subject' => $subject->name,
                    'type' => $type,
                    'difficulty' => 'medium',
                    'content' => 'Unanswered '.$type,
                    'options' => $type === 'short_answer' ? [] : ['A', 'B'],
                    'correct_answers' => [],
                    'correct_answer_single' => '',
                    'short_answer_text' => '',
                ])
                ->assertSessionHasErrors('correct_answers');
        }

        $this->assertDatabaseCount('question_bank_questions', 0);
    }

    public function test_selected_answer_must_match_an_available_option(): void
    {
        $teacher = $this->createUser(['role' => 'teacher']);
        $subject = $this->createSubject($teacher);

        $this->actingAs($teacher)
            ->post(route('question-bank.store'), [
                'subject' => $subject->name,
                'type' => 'single_choice',
                'difficulty' => 'medium',
                'content' => 'Choose an option.',
                'options' => ['A', 'B'],
                'correct_answer_single' => 'Not an option',
            ])
            ->assertSessionHasErrors('correct_answer_single');

        $this->actingAs($teacher)
            ->post(route('question-bank.store'), [
                'subject' => $subject->name,
                'type' => 'multiple_choice',
                'difficulty' => 'medium',
                'content' => 'Choose valid options.',
                'options' => ['A', 'B'],
                'correct_answers' => ['A', 'Not an option'],
            ])
            ->assertSessionHasErrors('correct_answers');

        $this->assertDatabaseCount('question_bank_questions', 0);
    }

    private function createSubject($creator): Subject
    {
        return Subject::query()->create([
            'created_by' => $creator->id,
            'name' => 'Mathematics',
            'code' => 'MATH',
            'slug' => 'mathematics',
            'status' => 'active',
        ]);
    }
}
