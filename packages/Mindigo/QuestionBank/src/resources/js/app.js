import '../../../../Core/src/resources/js/mindigo-ui.js';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-question-builder]').forEach((builder) => {
        const topicUrl = builder.dataset.topicUrl;
        const topicSelects = new Map(
            [...builder.querySelectorAll('[data-question-topic-select]')].map((select) => [select.dataset.topicName, select])
        );

        builder.querySelectorAll('[data-question-subject-select]').forEach((subjectSelect) => {
            const topicSelect = topicSelects.get(subjectSelect.dataset.topicTarget);
            if (!topicSelect) return;

            const field = topicSelect.closest('.question-field');
            const status = field?.querySelector('[data-question-topic-status]');
            const retry = field?.querySelector('[data-question-topic-retry]');
            const topicValue = field?.querySelector('[data-question-topic-value]');
            let controller = null;

            const setStatus = (message = '', state = '') => {
                if (!status) return;
                status.textContent = message;
                status.dataset.state = state;
                status.classList.toggle('hidden', !message);
                retry?.classList.toggle('hidden', state !== 'error');
            };

            const setPlaceholder = () => {
                topicSelect.replaceChildren();
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = topicSelect.dataset.placeholder || '';
                topicSelect.append(placeholder);
                topicSelect.value = '';
            };

            const loadTopics = async (restoreSelection = false) => {
                controller?.abort();
                controller = null;

                const subject = subjectSelect.value.trim();
                const current = restoreSelection ? (topicValue?.value || topicSelect.dataset.currentValue || '') : '';
                const currentId = restoreSelection ? (topicSelect.dataset.currentId || '') : '';
                if (!restoreSelection && topicValue) topicValue.value = '';
                topicSelect.dataset.currentValue = current;
                setPlaceholder();
                topicSelect.disabled = true;
                setStatus();

                if (!subject) return;

                controller = new AbortController();
                const requestController = controller;
                setStatus(builder.dataset.topicLoading, 'loading');

                try {
                    const url = new URL(topicUrl, window.location.href);
                    url.searchParams.set('subject', subject);
                    const response = await fetch(url, {
                        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        signal: requestController.signal,
                    });
                    let data = null;
                    try {
                        data = await response.json();
                    } catch {
                        data = null;
                    }

                    if (!response.ok || !Array.isArray(data?.topics)) {
                        throw new Error(data?.message || builder.dataset.topicError);
                    }

                    const topics = data.topics.filter((topic) => topic && typeof topic.name === 'string' && topic.name.trim());
                    topics.forEach((topic) => {
                        const option = document.createElement('option');
                        option.value = String(topic.id);
                        option.textContent = topic.name;
                        option.dataset.topicName = topic.name;
                        option.selected = currentId
                            ? String(topic.id) === currentId
                            : topic.name === current;
                        topicSelect.append(option);
                    });

                    const selectedTopic = topics.find((topic) => currentId
                        ? String(topic.id) === currentId
                        : topic.name === current);
                    if (topicValue) topicValue.value = selectedTopic?.name || '';
                    topicSelect.dataset.currentValue = selectedTopic?.name || '';
                    topicSelect.dataset.currentId = selectedTopic ? String(selectedTopic.id) : '';
                    if (!selectedTopic && topicValue) topicValue.value = '';
                    topicSelect.disabled = topics.length === 0;
                    if (topics.length === 0) setStatus(builder.dataset.topicEmpty, 'empty');
                    else setStatus();
                } catch (error) {
                    if (error instanceof Error && error.name === 'AbortError') return;
                    topicSelect.disabled = true;
                    const message = error instanceof Error && !['TypeError', 'SyntaxError'].includes(error.name)
                        ? error.message
                        : builder.dataset.topicError;
                    setStatus(message, 'error');
                }
            };

            topicSelect.addEventListener('change', () => {
                const option = topicSelect.selectedOptions[0];
                topicSelect.dataset.currentValue = option?.dataset.topicName || '';
                topicSelect.dataset.currentId = option?.value || '';
                if (topicValue) topicValue.value = option?.dataset.topicName || '';
            });
            subjectSelect.addEventListener('change', () => {
                topicSelect.dataset.currentValue = '';
                topicSelect.dataset.currentId = '';
                loadTopics();
            });
            retry?.addEventListener('click', () => loadTopics(true));
            loadTopics(true);
        });
    });

    document.querySelectorAll('[data-question-auto-submit]').forEach((field) => {
        field.addEventListener('change', () => field.form?.requestSubmit());
    });

    document.querySelectorAll('[data-folder-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelector('[data-folder-form]')?.classList.toggle('hidden');
        });
    });

    document.querySelectorAll('[data-import-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelector('[data-import-form]')?.classList.toggle('hidden');
        });
    });

    document.querySelectorAll('[data-import-drop]').forEach((dropZone) => {
        const fileInput = dropZone.querySelector('[data-import-file]');
        const fileName = dropZone.querySelector('[data-import-file-name]');

        const setFileName = () => {
            if (fileInput?.files?.[0] && fileName) {
                fileName.textContent = fileInput.files[0].name;
            }
        };

        fileInput?.addEventListener('change', setFileName);

        ['dragenter', 'dragover'].forEach((eventName) => {
            dropZone.addEventListener(eventName, (event) => {
                event.preventDefault();
                dropZone.classList.add('is-dragging');
            });
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            dropZone.addEventListener(eventName, (event) => {
                event.preventDefault();
                dropZone.classList.remove('is-dragging');
            });
        });

        dropZone.addEventListener('drop', (event) => {
            if (!fileInput || !event.dataTransfer?.files?.length) {
                return;
            }

            fileInput.files = event.dataTransfer.files;
            setFileName();
        });
    });

    document.querySelectorAll('[data-question-builder]').forEach((builder) => {
        const partLinks = [...builder.querySelectorAll('[data-question-part-link]')];
        const partCards = [...builder.querySelectorAll('[data-question-part]')];
        const questionIndex = builder.querySelector('[data-question-index]');
        const questionMain = builder.querySelector('.question-studio-main');
        const typeField = builder.querySelector('[data-question-type]');
        const optionPanel = builder.querySelector('[data-option-panel]');
        const shortAnswerPanel = builder.querySelector('[data-short-answer-panel]');
        const essayPanel = builder.querySelector('[data-essay-panel]');
        const optionList = builder.querySelector('[data-option-list]');
        const template = builder.querySelector('[data-option-template]');
        const addButton = builder.querySelector('[data-add-option]');

        const visiblePartCard = (part) => partCards.find((card) => card.dataset.questionPart === part && !card.classList.contains('hidden'));

        const setActiveQuestionIndex = (activeTarget) => {
            questionIndex?.querySelectorAll('[data-question-index-button]').forEach((button) => {
                button.classList.toggle('is-active', button.dataset.questionIndexButton === activeTarget.id);
            });
        };

        const renderQuestionIndex = () => {
            if (!questionIndex) {
                return;
            }

            const targets = [...builder.querySelectorAll('[data-question-index-target]')];
            questionIndex.innerHTML = '';

            targets.forEach((target, index) => {
                if (!target.id) {
                    target.id = `question-item-${index + 1}`;
                }

                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = String(index + 1);
                button.dataset.questionIndexButton = target.id;
                button.classList.toggle('is-active', index === 0);
                button.addEventListener('click', () => {
                    setActiveQuestionIndex(target);
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
                questionIndex.append(button);
            });
        };

        const setActivePart = (part) => {
            partLinks.forEach((link) => {
                link.classList.toggle('is-active', link.dataset.questionPartLink === part);
            });

            partCards.forEach((card) => {
                card.classList.toggle('is-highlight', card.dataset.questionPart === part && !card.classList.contains('hidden'));
            });
        };

        partLinks.forEach((link) => {
            link.addEventListener('click', (event) => {
                const part = link.dataset.questionPartLink;
                const target = visiblePartCard(part);

                if (!part || !target) {
                    return;
                }

                event.preventDefault();
                setActivePart(part);
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        const syncOptionValues = () => {
            optionList?.querySelectorAll('[data-option-row]').forEach((row, index) => {
                const optionInput = row.querySelector('[data-option-input]');
                const singleAnswer = row.querySelector('[data-single-answer]');
                const multipleAnswer = row.querySelector('[data-multiple-answer]');
                const placeholder = optionInput?.getAttribute('placeholder') || '';
                const value = optionInput?.value || '';

                if (singleAnswer) {
                    singleAnswer.value = value;
                }

                if (multipleAnswer) {
                    multipleAnswer.value = value;
                }

                if (optionInput && placeholder.includes('__INDEX__')) {
                    optionInput.setAttribute('placeholder', placeholder.replace('__INDEX__', String(index + 1)));
                }
            });
        };

        let lastAnswerType = typeField?.value || 'single_choice';

        const setTypeMode = () => {
            const type = typeField?.value || 'single_choice';
            const isShort = type === 'short_answer';
            const isEssay = type === 'essay';
            const isMultiple = type === 'multiple_choice';
            const isTrueFalse = type === 'true_false';

            optionPanel?.classList.toggle('hidden', isShort || isEssay);
            shortAnswerPanel?.classList.toggle('hidden', !isShort);
            essayPanel?.classList.toggle('hidden', !isEssay);

            optionList?.querySelectorAll('[data-option-row]').forEach((row, index) => {
                const singleAnswer = row.querySelector('[data-single-answer]');
                const multipleAnswer = row.querySelector('[data-multiple-answer]');
                const singleControl = row.querySelector('[data-single-answer-control]');
                const multipleControl = row.querySelector('[data-multiple-answer-control]');
                const optionInput = row.querySelector('[data-option-input]');
                const removeButton = row.querySelector('[data-remove-option]');

                const supportsSingleAnswer = type === 'single_choice' || isTrueFalse;
                if (lastAnswerType === 'multiple_choice' && !isMultiple && multipleAnswer?.checked && supportsSingleAnswer) {
                    singleAnswer.checked = true;
                } else if (lastAnswerType !== 'multiple_choice' && isMultiple && singleAnswer?.checked) {
                    multipleAnswer.checked = true;
                }

                singleControl?.classList.toggle('hidden', !supportsSingleAnswer);
                multipleControl?.classList.toggle('hidden', !isMultiple);
                if (singleAnswer) {
                    singleAnswer.disabled = !supportsSingleAnswer;
                }
                if (multipleAnswer) {
                    multipleAnswer.disabled = !isMultiple;
                }

                if (isTrueFalse && optionInput) {
                    optionInput.value = index === 0 ? 'True' : (index === 1 ? 'False' : '');
                    optionInput.readOnly = index < 2;
                    row.classList.toggle('hidden', index > 1);
                } else {
                    if (optionInput) {
                        optionInput.readOnly = false;
                    }
                    row.classList.remove('hidden');
                }

                if (removeButton) {
                    removeButton.disabled = isTrueFalse || (optionList.querySelectorAll('[data-option-row]').length <= 2);
                    removeButton.classList.toggle('opacity-40', removeButton.disabled);
                }
            });

            addButton?.classList.toggle('hidden', isShort || isEssay || isTrueFalse);
            syncOptionValues();

            const activePart = builder.querySelector('[data-question-part-link].is-active')?.dataset.questionPartLink || 'content';
            setActivePart(activePart);
            lastAnswerType = type;
        };

        addButton?.addEventListener('click', () => {
            if (!template || !optionList) {
                return;
            }

            const fragment = template.content.cloneNode(true);
            optionList.append(fragment);
            setTypeMode();
        });

        optionList?.addEventListener('input', (event) => {
            if (event.target.matches('[data-option-input]')) {
                syncOptionValues();
            }
        });

        optionList?.addEventListener('click', (event) => {
            const removeButton = event.target.closest('[data-remove-option]');
            if (!removeButton || removeButton.disabled) {
                return;
            }

            removeButton.closest('[data-option-row]')?.remove();
            setTypeMode();
        });

        typeField?.addEventListener('change', setTypeMode);
        setTypeMode();
        setActivePart('content');
        renderQuestionIndex();

        if (questionIndex) {
            const indexObserver = new MutationObserver(renderQuestionIndex);
            indexObserver.observe(questionMain || builder, { childList: true, subtree: true });
        }
    });
});
