function createButtonStartWorkingDay() {
   const timemanStart = document.querySelector(".tm-popup-button-handler");
   const timemanStartButton = timemanStart.querySelector("button");

   if (timemanStartButton) {
      const timemanStartNewButton = document.createElement("button");

      timemanStartButton.style.display = "none";

      timemanStartNewButton.classList.add("ui-btn", "ui-btn-success", "ui-btn-icon-start");
      timemanStartNewButton.textContent = 'Начать рабочий день';
      timemanStartNewButton.addEventListener("click", clickStartWorkingDayPopup);
      timemanStart.prepend(timemanStartNewButton);
   }
}

function clickStartWorkingDayPopup() {
   const buttonStartJobDay = document.createElement('button');
   buttonStartJobDay.className = 'ui-btn ui-btn-success ui-btn-icon-start';
   buttonStartJobDay.style.marginTop = '10px';
   buttonStartJobDay.innerText = 'Начать рабочий день';
   buttonStartJobDay.onclick = function() {
      BX.ajax({
         url: '/local/js/otus/StartWorkingDay/ajax.php',
         data: {
            type: 'start'
         },
         method: 'POST',
         onsuccess: (data) => {
            window.location.href = window.location.href;
         }
      });
   };

   const buttonEndJobDay = document.createElement('button');
   buttonEndJobDay.className = 'ui-btn ui-btn-danger ui-btn-icon-stop';
   buttonEndJobDay.style.marginLeft = '0';
   buttonEndJobDay.style.marginTop = '10px';
   buttonEndJobDay.innerText = 'Завершить рабочий день';
   buttonEndJobDay.onclick = function() {
      BX.ajax({
         url: '/local/js/otus/StartWorkingDay/ajax.php',
         data: {
            type: 'end'
         },
         method: 'POST',
         onsuccess: (data) => {
            window.location.href = window.location.href;
         }
      });
   };

   const messageBoxContent = document.createElement('div');
   messageBoxContent.style.display = 'flex';
   messageBoxContent.style.flexDirection = 'column';
   messageBoxContent.appendChild(buttonStartJobDay);
   messageBoxContent.appendChild(buttonEndJobDay);

   const messageBox = BX.UI.Dialogs.MessageBox.create(
       {
          message: messageBoxContent,
          title: "Начать рабочий день",
          popupOptions: {
             events: {
                onPopupClose: function() {
                   console.log(234234);
                }
             }
          }
       }
   );
   messageBox.show();
}

BX.addCustomEvent("onTimeManWindowOpen", createButtonStartWorkingDay);
