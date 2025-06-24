let questionCount = 0;
let formData = [];

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('addQuestionBtn').addEventListener('click', addQuestion);

  document.getElementById('eventForm').addEventListener('submit', function (e) {
    updateAllQuestionData();

    const questionsInput = document.createElement('input');
    questionsInput.type = 'hidden';
    questionsInput.name = 'questions_data';
    questionsInput.value = JSON.stringify(formData);
    this.appendChild(questionsInput);
  });
});

function addQuestion() {
  questionCount++;
  const questionId = `question_${questionCount}`;

  const questionHTML = `
    <div class="card border-0 shadow-sm mb-3 p-3 rounded-4" id="${questionId}" style="background-color: #f2f2f2;">
      <input type="text" class="form-control form-control-sm question-title rounded-3 mb-3"
             name="question_text_${questionCount}" placeholder="Enter your question"
             onchange="updateQuestionData('${questionId}')" required>

      <textarea class="form-control form-control-sm question-help rounded-3 mb-3"
                name="question_help_${questionCount}" placeholder="Help text (optional)" rows="2"
                onchange="updateQuestionData('${questionId}')"></textarea>

      <div class="d-flex justify-content-between align-items-center">
        <div class="form-check form-switch">
          <input class="form-check-input required-checkbox" type="checkbox"
                 name="question_required_${questionCount}" value="1"
                 id="required-${questionCount}" onchange="updateQuestionData('${questionId}')">
          <label class="form-check-label" for="required-${questionCount}">Required</label>
        </div>
        <button type="button" class="btn btn-primary btn-sm rounded-pill" onclick="removeQuestion('${questionId}')">
          Remove
        </button>
      </div>
      <input type="hidden" name="question_order_${questionCount}" value="${questionCount}">
    </div>
  `;

  document.getElementById('questionsContainer').insertAdjacentHTML('beforeend', questionHTML);
  updateQuestionData(questionId);
}

function removeQuestion(questionId) {
  document.getElementById(questionId)?.remove();
  formData = formData.filter(q => q.id !== questionId);
}

function updateQuestionData(questionId) {
  const container = document.getElementById(questionId);
  if (!container) return;

  const title = container.querySelector('.question-title')?.value || '';
  const helpText = container.querySelector('.question-help')?.value || '';
  const required = container.querySelector('.required-checkbox')?.checked || false;

  const data = { id: questionId, title, helpText, required };

  const existingIndex = formData.findIndex(q => q.id === questionId);
  if (existingIndex >= 0) {
    formData[existingIndex] = data;
  } else {
    formData.push(data);
  }
}

function updateAllQuestionData() {
  document.querySelectorAll('[id^="question_"]').forEach(container => {
    const questionId = container.id;
    updateQuestionData(questionId);
  });
}
