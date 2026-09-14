import * as bootstrap from 'bootstrap';
import Chart from 'chart.js/auto';

window.bootstrap = bootstrap;

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const showErrors = (form, errors = {}) => {
    form.querySelectorAll('[data-error]').forEach((node) => {
        node.textContent = '';
    });

    Object.entries(errors).forEach(([field, messages]) => {
        const target = form.querySelector(`[data-error="${field}"]`);
        if (target) {
            target.textContent = Array.isArray(messages) ? messages[0] : messages;
        }
    });
};

document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.matches('[data-ajax]')) {
        return;
    }

    event.preventDefault();

    if (form.hasAttribute('data-confirm') && !window.confirm(form.getAttribute('data-confirm'))) {
        return;
    }

    const submit = form.querySelector('[type="submit"]');
    const feedback = form.querySelector('[data-feedback]');
    const fileErrors = {};

    form.querySelectorAll('input[type="file"][data-max-kb]').forEach((input) => {
        const maxKb = Number(input.getAttribute('data-max-kb'));
        const file = input.files?.[0];
        if (file && maxKb && file.size > maxKb * 1024) {
            fileErrors[input.name] = `Dosya en fazla ${Math.round(maxKb / 1024)} MB olabilir.`;
        }
    });

    if (Object.keys(fileErrors).length) {
        showErrors(form, fileErrors);
        if (feedback) {
            feedback.textContent = 'Formu kontrol et.';
            feedback.classList.add('text-danger');
        }
        return;
    }

    submit?.setAttribute('disabled', 'disabled');
    if (feedback) {
        feedback.textContent = '';
        feedback.classList.remove('text-danger');
    }

    try {
        const body = new FormData(form);
        const response = await fetch(form.action, {
            method: form.method || 'POST',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf() || '',
            },
            body,
        });

        const payload = await response.json().catch(() => ({}));

        if (response.status === 422) {
            showErrors(form, payload.errors || {});
            if (feedback) {
                feedback.textContent = payload.message || 'Formu kontrol et.';
                feedback.classList.add('text-danger');
            }
            return;
        }

        if (!response.ok && response.status !== 409) {
            throw new Error(payload.message || 'İşlem tamamlanamadı.');
        }

        if (feedback) {
            feedback.textContent = payload.message || '';
            feedback.classList.toggle('text-danger', Boolean(payload.reused) || !response.ok);
        }

        if (payload.redirect) {
            window.location.href = payload.redirect;
        }
    } catch (error) {
        if (feedback) {
            feedback.textContent = error.message;
            feedback.classList.add('text-danger');
        }
    } finally {
        submit?.removeAttribute('disabled');
    }
});

const addEventCategory = async (root) => {
    const input = root.querySelector('[data-category-name]');
    const feedback = root.querySelector('[data-category-feedback]');
    const select = document.getElementById('event-category-select');
    const name = input?.value.trim();

    if (feedback) {
        feedback.textContent = '';
        feedback.classList.remove('text-danger');
    }

    if (!name) {
        if (feedback) {
            feedback.textContent = 'Tür adı yaz.';
            feedback.classList.add('text-danger');
        }
        return;
    }

    const response = await fetch(root.getAttribute('data-category-create'), {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf() || '',
        },
        body: JSON.stringify({ name }),
    });

    const payload = await response.json().catch(() => ({}));

    if (!response.ok) {
        throw new Error(payload.message || payload.errors?.name?.[0] || 'Tür eklenemedi.');
    }

    const category = payload.category;
    if (select && category) {
        let option = [...select.options].find((item) => item.value === String(category.id));
        if (!option) {
            option = new Option(category.name, category.id);
            select.append(option);
        }
        select.value = String(category.id);
    }

    if (input) {
        input.value = '';
    }

    if (feedback) {
        feedback.textContent = payload.message || 'Etkinlik türü eklendi.';
    }
};

document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-add-category]');
    if (!button) {
        return;
    }

    const root = button.closest('[data-category-create]');
    if (!root) {
        return;
    }

    event.preventDefault();
    button.setAttribute('disabled', 'disabled');

    try {
        await addEventCategory(root);
    } catch (error) {
        const feedback = root.querySelector('[data-category-feedback]');
        if (feedback) {
            feedback.textContent = error.message;
            feedback.classList.add('text-danger');
        }
    } finally {
        button.removeAttribute('disabled');
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Enter' || !event.target.matches('[data-category-name]')) {
        return;
    }

    event.preventDefault();
    event.target.closest('[data-category-create]')?.querySelector('[data-add-category]')?.click();
});

document.querySelectorAll('[data-chart]').forEach((canvas) => {
    const values = JSON.parse(canvas.getAttribute('data-chart') || '[]');
    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: values.map((item) => item.label),
            datasets: [{
                data: values.map((item) => item.sold),
                backgroundColor: values.map((_, index) => (
                    index === values.length - 1 ? '#040F0F' : '#85BDBF'
                )),
                borderRadius: 8,
                barPercentage: 0.55,
            }],
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { family: 'Manrope' } } },
                y: { display: false, grid: { display: false } },
            },
        },
    });
});
