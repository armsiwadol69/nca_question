// List 1
$("#items-1").sortable({
  group: { name: "item", pull: "clone", put: false },
  animation: 200,
  sort: false,
  onSort: reportActivity,
});

// List 2
$("#items-2").sortable({
  group: { name: "item", pull: "clone" },
  animation: 200,
  onSort: reportActivity,
  onEnd: handleOptionDrop,
  removeOnSpill: true
});


function handleOptionDrop(event) {
  const fromNestedQuestion = event.from.classList.contains('nested-sortable');
  const toNestedQuestion = event.to.classList.contains('nested-sortable');

  if (fromNestedQuestion && !toNestedQuestion) {
    // Option removed from a question
    event.item.parentNode.removeChild(event.item);
  } else if (!fromNestedQuestion && toNestedQuestion) {
    // Option dropped into a question
    const option = event.item.cloneNode(true);
    option.contentEditable = true; // Make the dropped item editable
    option.textContent = 'Type your text here'; // Initial placeholder text
    event.item.parentNode.removeChild(event.item);
    event.to.appendChild(option);
  }
}

// Arrays of "data-id"
$("#get-order").click(function () {
  var sort1 = $("#items-1").sortable("toArray");
  console.log(sort1);
  document.getElementById("left-sort").innerHTML = JSON.stringify(sort1);
  var sort2 = $("#items-2").sortable("toArray");
  document.getElementById("right-sort").innerHTML = JSON.stringify(sort2);
  console.log(sort2);
});

// Report when the sort order has changed
function reportActivity() {
  console.log("The sort order has changed");
}

function initSortable() {
  var nestedSortables = [].slice.call(
    document.querySelectorAll(".nested-sortable")
  );
  for (var i = 0; i < nestedSortables.length; i++) {
    var group = i === 0 ? "top-level" : "nested-" + i; // Define different groups for each level
    // console.log(group);
    new Sortable(nestedSortables[i], {
      // group: { name: "QQQQ" }, // Set different groups for each level
      group: { name: group }, // Set different groups for each level
      animation: 150,
      fallbackOnBody: true,
      swapThreshold: 0.65,
    });
  }
}

function intiCreatePanle() {
  const target = document.getElementById("createFormPanel");
  new Sortable(target, {
    group: {
      name: "shared",
      pull: "clone", // To clone: set pull to 'clone'
    },
    animation: 150,
  });
}

function serializeElement(element) {
  const setId = element.getAttribute("data-id");
  const set = setId.replace("set", "");
  const setName = element.textContent.trim().split("\n")[0].trim(); // Extracting only the set name without other content

  const obj = {
    set,
    setId,
    setName,
    question: [],
  };

  const questionItems = Array.from(
    element.querySelectorAll(".question[data-id]")
  );
  
  questionItems.forEach((questionItem) => {
    const questionDataId = questionItem.getAttribute("data-id");
    const questionText = questionItem.innerHTML
      .trim()
      .split("<")[0]
      .trim()
      .replaceAll("\n                    ", " "); //REMOVE UNNECESSARY WHITE SPACE
    // console.log(questionItem.innerHTML.trim().split('<'));

    const questionObj = {
      question_id: questionDataId,
      question_text: questionText,
      answer: [],
    };

    const answerItems = Array.from(
      questionItem.querySelectorAll(".answer[data-id]")
    );
    answerItems.forEach((answerItem) => {
      const answerId = answerItem.getAttribute("data-id");
      const answerText = answerItem.textContent.trim();
      let answerStatus;
      if (answerItem.classList.contains("text-bg-success")) {
        answerStatus = true;
      } else {
        answerStatus = false;
      }
      questionObj.answer.push({
        answer_id: answerId,
        answer_text: answerText,
        answer_check: answerStatus,
      });
    });

    obj.question.push(questionObj);
  });

  return obj;
}

function serialize(root) {
  const nestedItems = Array.from(root.querySelectorAll(".setName[data-id]"));
  const result = nestedItems.map((item) => serializeElement(item));
  return result;
}

function createHTMLFromJSON(data) {
  let html = '<div id="nestedQuestion" class="list-group col">';
  let setOfquestions = 1;
  for (let i = 0; i < data.length; i++) {
    const set = data[i];
    html += `<div class="list-group-item nested-sortable setName" data-id="${set.setId}">
                  <div class="list-group-item nested-sortable">${set.setName}`;
    setOfquestions++;

    let numberOfQuestion = 1;
    for (let j = 0; j < set.question.length; j++) {
      const question = set.question[j];
      html += `<div class="list-group-item nested-sortable question" data-id="${question.question_id}">
                    ${numberOfQuestion}. ${question.question_text}
                    <div class="list-group nested-sortable">`;
      numberOfQuestion++;

      let numberOfChoice = 1;
      for (let k = 0; k < question.answer.length; k++) {
        const answer = question.answer[k];
        const checked = answer.answer_check ? "text-bg-success" : "";
        html += `<div class="list-group-item nested-sortable answer ${checked}" data-id="${answer.answer_id}">
                      ${numberOfChoice}. ${answer.answer_text}
                    </div>`;
        numberOfChoice++;
      }

      html += `</div></div>`;
    }

    html += `</div></div>`;
  }

  return html;
}

function createSortableByJson() {
  const jsonData = document.getElementById("Nested-Sort").innerHTML.trim();
  if (jsonData === "") {
    Swal.fire({
      title: "Did you read that RED text?",
      html: "Gen JSON from above before click this button! <br> It's Empty! What do you want me to do??? <br> That's Why She left you alone!",
      icon: "error",
    });
    return;
  }
  document.getElementById("placeholder-sortable").innerHTML =
    createHTMLFromJSON(JSON.parse(jsonData));
  initSortable();
}

$("#get-nested-btn").click(function () {
  const rootElement = document.getElementById("nestedQuestion");
  const serializedData = serialize(rootElement);
  console.log(JSON.stringify(serializedData, null, 2));
  document.getElementById("Nested-Sort").innerHTML = JSON.stringify(
    serializedData,
    null,
    2
  );
});
