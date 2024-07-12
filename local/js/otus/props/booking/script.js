function booking() {
    const buttons = [...document.querySelectorAll('.booking-add-button')];

    buttons.forEach((button) => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            e.preventDefault();
            const procedureId = e.target.dataset.procedureId;

            const messageBox = BX.UI.Dialogs.MessageBox.create(
                {
                    message: `
                        <div class="ui-form ui-form-section" style="padding-bottom: 0;">
                            <div class="ui-form-row">
                                <div class="ui-form-content">
                                    <div class="ui-ctl ui-ctl-textbox ui-ctl-w100">
                                        <input id="js-booking-fio" type="text" class="ui-ctl-element" placeholder="ФИО Клиента">
                                    </div>
                                </div>
                            </div>
                            <div class="ui-form-row">
                                <div class="ui-form-content">
                                    <div class="ui-ctl ui-ctl-after-icon ui-ctl-w100">
                                        <div class="ui-ctl-after ui-ctl-icon-calendar"></div>
                                        <div class="ui-ctl-element" >
                                            <input 
                                                id="js-booking-date"
                                                class="ui-ctl-element" 
                                                style="border: none; padding: 0;"
                                                type="text" 
                                                placeholder="Время записи" name="date"
                                                onclick="BX.calendar({node: this, field: this, bTime: true});">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ui-form-row">
                                <div id="js-booking-date-error" class="ui-alert ui-alert-danger" style="display: none;">
                                    <span class="ui-alert-message"><strong>Внимание!</strong> На это время уже запланирована процедура.</span>
                                </div>
                                <div id="js-booking-fio-date-error" class="ui-alert ui-alert-danger" style="display: none;">
                                    <span class="ui-alert-message">
                                        <strong>Внимание!</strong> Заполните ФИО и время записи
                                    </span>
                                </div>
                            </div>
                        </div>
                    `,
                    title: "Запись клиента",
                    buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
                    onOk: function (messageBox, button) {

                        BX.showWait();

                        const fio = document.querySelector('#js-booking-fio');
                        const date = document.querySelector('#js-booking-date');
                        const dateError = document.querySelector('#js-booking-date-error');
                        const fioDateError = document.querySelector('#js-booking-fio-date-error');

                        dateError.style.display = 'none';
                        fioDateError.style.display = 'none';

                        const data = {
                            fio: fio.value,
                            date: date.value,
                            procedureId: procedureId
                        };

                        if (!data.fio.length || !data.date.length) {
                            fioDateError.style.display = 'block';
                            button.setWaiting(false);
                        } else {
                            BX.ajax({
                                url: '/local/js/otus/props/booking/add_element.php',
                                data: data,
                                method: 'POST',
                                onsuccess: (data) => {
                                    const response = JSON.parse(data);
                                    button.setWaiting(false);
                                    if (response.success) {
                                        window.location.href = 'https://otus-dev.qwelp.ru/services/lists/22/view/0/';
                                        messageBox.close();
                                    } else {
                                        dateError.style.display = 'block';
                                    }
                                }
                            });
                        }

                        BX.closeWait();
                    },
                }
            );
            messageBox.show();
        });
    });
}

document.addEventListener("DOMContentLoaded", () => {
    booking();
});
