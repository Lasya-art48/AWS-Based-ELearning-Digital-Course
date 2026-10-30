const formatDateValue = (value) => {
    if (!value) {
        return '';
    }

    if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
        return value;
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return '';
    }

    return [
        date.getFullYear(),
        String(date.getMonth() + 1).padStart(2, '0'),
        String(date.getDate()).padStart(2, '0'),
    ].join('-');
};

const formatDateDisplay = (value) => {
    const isoDate = formatDateValue(value);

    if (!isoDate) {
        return '';
    }

    const [year, month, day] = isoDate.split('-');

    return `${day}/${month}/${year}`;
};

const openNativeDatePicker = (picker) => {
    if (!picker) {
        return;
    }

    if (typeof picker.showPicker === 'function') {
        picker.showPicker();
        return;
    }

    picker.click();
};

const fillScheduleForm = (data = {}) => {
    const title = document.getElementById('schedule-modal-title');
    const method = document.getElementById('schedule-form-method');
    const form = document.getElementById('schedule-form');
    const titleInput = document.getElementById('schedule-title');
    const dateInput = document.getElementById('schedule-date');
    const dateDisplay = document.getElementById('schedule-date-display');
    const datePicker = document.getElementById('schedule-date-picker');
    const startInput = document.getElementById('schedule-start');
    const endInput = document.getElementById('schedule-end');
    const descInput = document.getElementById('schedule-desc');
    const typeInput = document.getElementById('schedule-type');
    const makeupReasonInput = document.getElementById('schedule-makeup-reason');
    const substituteTeacherInput = document.getElementById('schedule-substitute-teacher');

    if (!form || !method) {
        return;
    }

    title && (title.textContent = data.modalTitle || '');
    method.value = data.method || 'POST';
    form.action = data.action || form.dataset.storeUrl || form.action;
    titleInput && (titleInput.value = data.title || '');
    const dateValue = formatDateValue(data.sessionDate) || form.dataset.defaultDate || '';
    dateInput && (dateInput.value = dateValue);
    dateDisplay && (dateDisplay.value = formatDateDisplay(dateValue));
    datePicker && (datePicker.value = dateValue);
    startInput && (startInput.value = (data.startTime || form.dataset.defaultStart || '08:00').substring(0, 5));
    endInput && (endInput.value = (data.endTime || form.dataset.defaultEnd || '10:00').substring(0, 5));
    descInput && (descInput.value = data.description || '');
    typeInput && (typeInput.value = data.type || 'regular');
    makeupReasonInput && (makeupReasonInput.value = data.makeupReason || '');
    substituteTeacherInput && (substituteTeacherInput.value = data.substituteTeacherId || '');
    typeInput?.dispatchEvent(new Event('change', { bubbles: true }));
};

document.addEventListener('click', (event) => {
    const addTrigger = event.target.closest('[data-schedule-add]');
    if (addTrigger) {
        fillScheduleForm({
            modalTitle: addTrigger.dataset.modalTitle,
            method: 'POST',
            action: addTrigger.dataset.storeUrl,
            sessionDate: addTrigger.dataset.defaultDate,
            startTime: addTrigger.dataset.defaultStart,
            endTime: addTrigger.dataset.defaultEnd,
        });
        window.MindigoOpenModal?.('schedule-modal');
        return;
    }

    const editTrigger = event.target.closest('[data-schedule-edit]');
    if (editTrigger) {
        fillScheduleForm({
            modalTitle: editTrigger.dataset.modalTitle,
            method: 'PUT',
            action: editTrigger.dataset.updateUrl,
            title: editTrigger.dataset.title,
            sessionDate: editTrigger.dataset.sessionDate,
            startTime: editTrigger.dataset.startTime,
            endTime: editTrigger.dataset.endTime,
            description: editTrigger.dataset.description,
            type: editTrigger.dataset.type,
            makeupReason: editTrigger.dataset.makeupReason,
            substituteTeacherId: editTrigger.dataset.substituteTeacherId,
        });
        window.MindigoOpenModal?.('schedule-modal');
    }
});

const scheduleType = document.getElementById('schedule-type');
const scheduleMakeupReasonField = document.getElementById('schedule-makeup-reason-field');
const scheduleMakeupReason = document.getElementById('schedule-makeup-reason');

scheduleType?.addEventListener('change', () => {
    const isMakeup = scheduleType.value === 'makeup';
    scheduleMakeupReasonField?.classList.toggle('hidden', !isMakeup);
    if (scheduleMakeupReason) {
        scheduleMakeupReason.required = isMakeup;
        if (!isMakeup) scheduleMakeupReason.value = '';
    }
});

const scheduleDateValue = document.getElementById('schedule-date');
const scheduleDateDisplay = document.getElementById('schedule-date-display');
const scheduleDatePicker = document.getElementById('schedule-date-picker');
const scheduleDateTrigger = document.getElementById('schedule-date-trigger');

[scheduleDateDisplay, scheduleDateTrigger].forEach((control) => {
    control?.addEventListener('click', () => openNativeDatePicker(scheduleDatePicker));
});

scheduleDatePicker?.addEventListener('change', () => {
    const value = formatDateValue(scheduleDatePicker.value);
    if (scheduleDateValue) scheduleDateValue.value = value;
    if (scheduleDateDisplay) scheduleDateDisplay.value = formatDateDisplay(value);
});

document.querySelectorAll('[data-attendance-date-form]').forEach((form) => {
    const valueInput = form.querySelector('[data-attendance-date-value]');
    const displayInput = form.querySelector('[data-attendance-date-display]');
    const picker = form.querySelector('[data-attendance-date-picker]');
    const trigger = form.querySelector('[data-attendance-date-trigger]');

    [displayInput, trigger].forEach((control) => {
        control?.addEventListener('click', () => openNativeDatePicker(picker));
    });

    picker?.addEventListener('change', () => {
        const value = formatDateValue(picker.value);
        if (!value) return;

        valueInput.value = value;
        displayInput.value = formatDateDisplay(value);
        form.requestSubmit();
    });
});

document.querySelectorAll('[data-course-plan-form]').forEach((form) => {
    const valueInput = form.querySelector('[data-course-plan-date]');
    const displayInput = form.querySelector('[data-course-plan-date-display]');
    const picker = form.querySelector('[data-course-plan-date-picker]');
    const trigger = form.querySelector('[data-course-plan-date-trigger]');

    [displayInput, trigger].forEach((control) => control?.addEventListener('click', () => openNativeDatePicker(picker)));
    picker?.addEventListener('change', () => {
        const value = formatDateValue(picker.value);
        if (!value) return;

        valueInput.value = value;
        displayInput.value = formatDateDisplay(value);
    });
});

const normalizeSubjectSearch = (value) => value
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLocaleLowerCase();

document.querySelectorAll('[data-classroom-subject-picker]').forEach((picker) => {
    const trigger = picker.querySelector('[data-classroom-subject-trigger]');
    const panel = picker.querySelector('[data-classroom-subject-panel]');
    const search = picker.querySelector('[data-classroom-subject-search]');
    const select = picker.querySelector('[data-classroom-subject-select]');
    const label = picker.querySelector('[data-classroom-subject-label]');
    let options = [...picker.querySelectorAll('[data-classroom-subject-option]')];
    const empty = picker.querySelector('[data-classroom-subject-empty]');
    const loading = picker.querySelector('[data-classroom-subject-loading]');
    const error = picker.querySelector('[data-classroom-subject-error]');
    const errorMessage = picker.querySelector('[data-classroom-subject-error-message]');
    const defaultErrorMessage = errorMessage?.textContent || '';
    const retry = picker.querySelector('[data-classroom-subject-retry]');
    const unavailable = picker.querySelector('[data-classroom-subject-none]');
    const list = picker.querySelector('[role="listbox"]');
    let requestController = null;
    let loaded = false;
    let debounceTimer = null;

    const close = () => {
        panel?.classList.add('hidden');
        trigger?.setAttribute('aria-expanded', 'false');
    };

    const setState = (state) => {
        loading?.classList.toggle('hidden', state !== 'loading');
        error?.classList.toggle('hidden', state !== 'error');
        unavailable?.classList.toggle('hidden', state !== 'empty');
        empty?.classList.toggle('hidden', state !== 'no-results');
    };

    const renderSubjects = (subjects) => {
        const selectedId = select.value;
        const selectedOption = [...select.options].find((option) => option.value === selectedId);
        const selected = subjects.find((subject) => String(subject.id) === selectedId);
        const selectOptions = [select.options[0]];
        const optionButtons = document.createDocumentFragment();

        subjects.forEach((subject) => {
            const option = document.createElement('option');
            option.value = subject.id;
            option.textContent = subject.name;
            option.selected = String(subject.id) === selectedId;
            selectOptions.push(option);

            const button = document.createElement('button');
            button.type = 'button';
            button.setAttribute('role', 'option');
            button.dataset.classroomSubjectOption = '';
            button.dataset.value = subject.id;
            button.dataset.label = subject.name;
            button.dataset.search = `${subject.name} ${subject.code || ''}`;
            button.className = 'flex w-full items-center gap-2 rounded-xl px-3 py-2.5 text-left text-sm font-bold text-slate-700 transition hover:bg-green-50 hover:text-green-700';
            const color = document.createElement('span');
            color.className = 'h-2.5 w-2.5 shrink-0 rounded-full';
            color.style.backgroundColor = subject.color || '#16a34a';
            const name = document.createElement('span');
            name.className = 'min-w-0 flex-1 truncate';
            name.textContent = subject.name;
            const code = document.createElement('span');
            code.className = 'shrink-0 text-xs font-semibold text-slate-400';
            code.textContent = subject.code || '';
            button.append(color, name, code);
            optionButtons.append(button);
        });

        if (selectedOption && !selected) {
            const retainedOption = selectedOption.cloneNode(true);
            retainedOption.selected = true;
            selectOptions.push(retainedOption);
        }
        select.replaceChildren(...selectOptions);
        list.querySelectorAll('[data-classroom-subject-option]').forEach((option) => option.remove());
        list.insertBefore(optionButtons, empty);
        options = [...picker.querySelectorAll('[data-classroom-subject-option]')];
        if (selected) {
            label.textContent = selected.name;
            label.classList.remove('text-slate-400');
        }
        loaded = true;
        setState(subjects.length ? 'ready' : 'empty');
        filterOptions();
    };

    const filterOptions = () => {
        const keyword = normalizeSubjectSearch(search.value.trim());
        let visible = 0;
        options.forEach((option) => {
            const matches = normalizeSubjectSearch(option.dataset.search || option.dataset.label || '').includes(keyword);
            option.classList.toggle('hidden', !matches);
            if (matches) visible += 1;
        });
        setState(visible
            ? 'ready'
            : (loaded
                ? (options.length || search.value.trim() ? 'no-results' : 'empty')
                : 'loading'));
    };

    const loadSubjects = async () => {
        requestController?.abort();
        requestController = new AbortController();
        setState('loading');

        try {
            const url = new URL(picker.dataset.subjectsUrl, window.location.origin);
            if (search.value.trim()) url.searchParams.set('search', search.value.trim());
            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
                signal: requestController.signal,
            });
            if (!response.ok) {
                const requestError = new Error(`Subject request failed with status ${response.status}`);
                requestError.status = response.status;
                throw requestError;
            }
            const payload = await response.json();
            if (!Array.isArray(payload.data)) throw new Error('Subject response did not contain a data list');
            renderSubjects(payload.data);
        } catch (requestError) {
            if (requestError.name === 'AbortError') return;
            loaded = false;
            errorMessage.textContent = requestError.status
                ? `${defaultErrorMessage} (HTTP ${requestError.status})`
                : defaultErrorMessage;
            setState('error');
        }
    };

    trigger?.addEventListener('click', () => {
        const opening = panel?.classList.contains('hidden');
        panel?.classList.toggle('hidden', !opening);
        trigger.setAttribute('aria-expanded', opening ? 'true' : 'false');
        if (opening) {
            window.requestAnimationFrame(() => search?.focus());
            loadSubjects();
        }
    });

    search?.addEventListener('input', () => {
        if (debounceTimer) window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(loadSubjects, 250);
    });
    retry?.addEventListener('click', loadSubjects);

    list?.addEventListener('click', (event) => {
        const option = event.target.closest('[data-classroom-subject-option]');
        if (!option) return;

        select.value = option.dataset.value || '';
        select.dispatchEvent(new Event('change', { bubbles: true }));
        label.textContent = option.dataset.label || '';
        label.classList.remove('text-slate-400');
        search.value = '';
        options.forEach((item) => item.classList.toggle('hidden', item !== option));
        empty?.classList.add('hidden');
        close();
        trigger?.focus();
    });

    picker.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') close();
    });

    document.addEventListener('click', (event) => {
        if (!picker.contains(event.target)) close();
    });
});

document.querySelectorAll('[data-classroom-assistant-picker]').forEach((picker) => {
    const trigger = picker.querySelector('[data-classroom-assistant-trigger]');
    const panel = picker.querySelector('[data-classroom-assistant-panel]');
    const search = picker.querySelector('[data-classroom-assistant-search]');
    const select = picker.querySelector('[data-classroom-assistant-select]');
    const label = picker.querySelector('[data-classroom-assistant-label]');
    const options = [...picker.querySelectorAll('[data-classroom-assistant-option]')];
    const empty = picker.querySelector('[data-classroom-assistant-empty]');
    const close = () => {
        panel?.classList.add('hidden');
        trigger?.setAttribute('aria-expanded', 'false');
    };

    trigger?.addEventListener('click', () => {
        const opening = panel?.classList.contains('hidden');
        panel?.classList.toggle('hidden', !opening);
        trigger.setAttribute('aria-expanded', opening ? 'true' : 'false');
        if (opening) window.requestAnimationFrame(() => search?.focus());
    });
    search?.addEventListener('input', () => {
        const keyword = normalizeSubjectSearch(search.value.trim());
        let visible = 0;
        options.forEach((option) => {
            const matches = normalizeSubjectSearch(option.dataset.search || option.dataset.label || '').includes(keyword);
            option.classList.toggle('hidden', !matches);
            if (matches) visible += 1;
        });
        empty?.classList.toggle('hidden', visible > 0);
    });
    options.forEach((option) => option.addEventListener('click', () => {
        select.value = option.dataset.value || '';
        label.textContent = option.dataset.label || '';
        label.classList.toggle('text-slate-400', !select.value);
        label.classList.toggle('text-slate-800', Boolean(select.value));
        close();
        trigger?.focus();
    }));
    picker.addEventListener('keydown', (event) => event.key === 'Escape' && close());
    document.addEventListener('click', (event) => !picker.contains(event.target) && close());
});
