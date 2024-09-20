(() => {
	let isHandlerAttached = false; // Флаг для проверки, был ли обработчик уже добавлен
	let isHandlerAttached2 = false; // Флаг для проверки, был ли обработчик уже добавлен

	/**
	 * Ищем dealId в адресе
	 */
	const currentUrl = window.location.href;
	const urlParts = currentUrl.split('/');
	const dealIdIndex = urlParts.findIndex(part => part === 'details') + 1;
	const dealId = parseInt(urlParts[dealIdIndex], 10);

	/**
	 * Генерирует HTML-разметку для отображения информации об автомобиле.
	 *
	 * @param {Object} auto - Объект, содержащий данные об автомобиле.
	 * @param {string} auto.NAME - Название марки автомобиля.
	 * @param {string} auto.MODEL_VALUE - Модель автомобиля.
	 * @param {number} auto.YEAR_VALUE - Год выпуска автомобиля.
	 * @param {string} auto.TSVET_VALUE - Цвет автомобиля.
	 * @param {number} auto.PROBEG_VALUE - Пробег автомобиля.
	 *
	 * @returns {string} HTML-строка с информацией об автомобиле.
	 */
	const generateHTML = (auto) => {
		return `
        <div class='crm-entity-widget-participants-block' id='client-auto-info'>
            <div class='crm-entity-widget-inner crm-entity-widget-inner_client-auto-manager'>
                <ul class='client-auto-manager__items'>
                    <li>Марка: ${auto.NAME}</li>
                    <li>Модель: ${auto.MODEL_VALUE}</li>
                    <li>Год: ${auto.YEAR_VALUE}</li>
                    <li>Цвет: ${auto.TSVET_VALUE}</li>
                    <li>Пробег: ${auto.PROBEG_VALUE}</li>
                    <li><a href="#" class="view-history" data-row-id="${auto.ID}" data-model="${auto.MODEL_VALUE}">Посмотреть историю</a></li>
                </ul>
            </div>
        </div>
    `;
	};

	/**
	 * Открывает панель для выбора автомобиля и обрабатывает выбор автомобиля клиентом.
	 *
	 * @param {number|string} contactId - Идентификатор контакта (клиента) в CRM.
	 *
	 * @returns {void}
	 */
	function addAuto(contactId) {
		if (!contactId) {
			return;
		}

		BX.SidePanel.Instance.open(`/crm/contact/details/${contactId}/?tab=garage`, {
			events: {}
		});

		function onMessageHandler(event) {
			const autoId = event.getData().elementId;

			if (!autoId) {
				return;
			}

			BX.ajax.runComponentAction('otusdev:client.auto.manager',
				'getClientAutoById', {
					mode: 'class',
					data: {
						post: {
							autoId: autoId,
						}
					}
				})
				.then((response) => {
					if (response.status === "success") {
						console.log(response.data);

						const inputClientAuto = document.querySelector('[name="UF_CLIENT_AUTO"]');

						if (inputClientAuto) {
							inputClientAuto.value = autoId;
							const html = generateHTML(response.data);

							const existingInfo = document.getElementById('client-auto-info');
							if (existingInfo) {
								existingInfo.remove();
							}

							inputClientAuto.insertAdjacentHTML('afterend', html);

							// Триггерим событие изменения, чтобы Bitrix зарегистрировал это изменение
							const event = new Event('change', {bubbles: true});
							inputClientAuto.dispatchEvent(event);
						}
					}
				});
		}

		// Проверяем, не добавлен ли уже обработчик
		if (!isHandlerAttached2) {
			// Добавляем только один раз
			BX.addCustomEvent('SidePanel.Slider:onMessage', onMessageHandler);
			isHandlerAttached2 = true; // Устанавливаем флаг
		}
	}

	// Проверка, что машина принадлежит клиенту, срабатывает после сохранения сделки
	BX.addCustomEvent('onCrmEntityUpdate', (event) => {
		if (event.entityId === dealId && event.entityData && !event.entityData.UF_CLIENT_AUTO.IS_EMPTY) {
			const clientAutoId = event.entityData.UF_CLIENT_AUTO.VALUE[0];
			const contactId = event.entityData.CONTACT_ID;

			BX.ajax.runComponentAction('otusdev:client.auto.manager',
				'isAutoOwnedByClient', {
					mode: 'class',
					data: {
						post: {
							dealId: dealId,
							contactId: contactId,
							clientAutoId: clientAutoId,
						}
					}
				})
				.then((response) => {
					if (response.status === "success") {
						console.log(response.data);
					}
				});
		}
	});

	// Обработчик выбора элемента из выпадающего списка контактов
	BX.addCustomEvent('BX.UI.Dropdown:onSelect', function (editor) {
		// Получаем выбранный элемент
		const contactId = editor.CurrentItem.id;

		if (contactId) {
			// Пытаемся найти существующее скрытое поле CONTACT_ID
			let contactInput = document.querySelector('input[name="CONTACT_ID"]');

			if (!contactInput) {
				// Если поле не существует, создаем его
				contactInput = document.createElement('input');
				contactInput.type = 'hidden';
				contactInput.name = 'CONTACT_ID';
				contactInput.value = contactId;

				// Находим форму сделки, чтобы добавить поле
				const dealForm = document.querySelector('form'); // Убедитесь, что селектор верен
				if (dealForm) {
					dealForm.appendChild(contactInput);
					console.log('Скрытое поле CONTACT_ID создано и добавлено в форму.');
				} else {
					console.error('Форма сделки не найдена. Не удалось добавить скрытое поле CONTACT_ID.');
				}
			} else {
				// Если поле существует, обновляем его значение
				contactInput.value = contactId;
				console.log('Скрытое поле CONTACT_ID обновлено:', contactId);
			}

			// Дополнительно: триггерим событие изменения, если это необходимо
			if (contactInput) {
				const eventChange = new Event('change', {bubbles: true});
				contactInput.dispatchEvent(eventChange);
			}

			console.log(`contactId выбранного контакта: ${contactId}`);
		} else {
			console.error('Выбранный элемент не содержит идентификатора contactId.');
		}
	});

	// Клик по кнопке добавить автомобиль
	BX.addCustomEvent('BX.Crm.EntityEditor:onUserFieldsDeployed', function (editor) {
		// Проверяем, не был ли обработчик уже добавлен

		if (!isHandlerAttached) {
			document.addEventListener('click', (event) => {
				if (event.target.id === 'client-auto-manager-button-add') {
					event.preventDefault();

					let contactId = editor._model._data.CONTACT_ID;
					const contactInput = document.querySelector('input[name="CONTACT_ID"]');
					if (contactInput) {
						contactId = contactInput.value;
					}

					if (contactId) {
						addAuto(contactId);
					} else {
						// Получаем ссылку на редактор карточки сделки
						const editor = BX.Crm.EntityEditor.getDefault();
						const field = editor.getControlById("UF_CLIENT_AUTO");

						if (field) {
							// Показываем ошибку для данного поля
							field.showError("Нужно выбрать контакт");
						}
					}
				}
			});
			isHandlerAttached = true; // Устанавливаем флаг после добавления обработчика
		}
	});

	// Клик по ссылке. Посмотреть историю сделок по автомобилю
	document.addEventListener('click', function (event) {
		if (event.target.classList.contains('view-history')) {
			event.preventDefault();

			// Получаем параметры rowId и model из атрибутов data
			const rowId = event.target.getAttribute('data-row-id');
			const model = event.target.getAttribute('data-model');

			// Вызываем попап с историей сделок
			if (BX.GetDealAutoById && BX.GetDealAutoById.DealPopup) {
				BX.GetDealAutoById.DealPopup.openPopup(rowId, model);
			} else {
				console.error('BX.GetDealAutoById.DealPopup не определен');
			}
		}
	});
})();
