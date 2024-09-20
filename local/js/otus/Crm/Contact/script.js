(() => {
	let isActionsHandlerAttached = false; // Флаг для отслеживания состояния

	// Добавление пункта меню в списке автомобилей, для выбора автомобиля в сделке
	function addCustomActions() {
		BX.Main.gridManager.data.forEach(grid => {
			const gridItem = BX.Main.gridManager.getInstanceById(grid.id);
			const rows = gridItem.getRows().getBodyChild();
			rows.forEach(row => {
				const elementId = row.getNode().getAttribute('data-id');
				const actions = row.getActions();
				actions.push({
					text: 'Выбрать',
					onclick: function () {
						BX.SidePanel.Instance.postMessage(window, 'element-selected', {
							elementId: elementId
						});
						BX.SidePanel.Instance.close();
					}
				});
				row.setActions(actions);
			});
		});
	}

	// Клик по модели, чтобы раскрылось popup окна со сделками по автомобилю
	function addPopupEventHandlers() {
		const popupLinks = document.querySelectorAll('.popup-link');

		popupLinks.forEach((link) => {
			link.addEventListener('click', function (event) {
				event.preventDefault();

				const rowId = this.getAttribute('data-row-id');
				const model = this.getAttribute('data-model');

				BX.GetDealAutoById.DealPopup.openPopup(rowId, model);
			});
		});
	}

	// Функция для обновления ячеек
	function updateGridCells(gridInstance) {
		const rows = gridInstance.getRows().getBodyChild();

		rows.forEach((row) => {
			const cells = row.getCells();
			const columnIndex = 3; // Укажите нужный индекс столбца
			const autoId = row.checkbox.defaultValue;

			if (cells[columnIndex]) {
				const td = cells[columnIndex].querySelector('.main-grid-cell-content');
				const model = td.textContent;

				td.innerHTML = `<a href="#" class="popup-link" data-row-id="${autoId}" data-model="${model}">${model}</a>`;
			}
		});

		addPopupEventHandlers();
	}

	// Проверяем, не добавлен ли уже обработчик
	if (!isActionsHandlerAttached) {
		BX.addCustomEvent('Grid::updated', addCustomActions);
		isActionsHandlerAttached = true; // Устанавливаем флаг
	}

	// Клик по вкладке Гараж. Происходит по клику в карточке сделки, чтобы сразу открылась вкладка гараж
	BX.addCustomEvent('BX.Crm.EntityEditor:onInit', (editor) => {
		setTimeout(() => {
			const params = new URLSearchParams(window.location.search);
			const tab = params.get('tab');
			const contactId = editor._entityId;

			if (tab === 'garage') {
				const button = document.querySelector('#crm_scope_detail_c_contact__tab_lists_26');

				if (button) {
					button.click();

					setTimeout(() => {
						addCustomActions();
					}, 1000);
				}
			}
		}, 100);
	});

	// Добавляем обработчик события Grid::ready
	BX.addCustomEvent('Grid::ready', function (eventData) {
		var gridInstance = eventData;

		if (gridInstance && gridInstance.getId() === 'lists_attached_crm_26') {
			// Вызываем функцию обновления ячеек
			updateGridCells(gridInstance);

			// Добавляем обработчик обновления грида для динамических изменений
			BX.addCustomEvent('Grid::updated', function (eventData) {
				if (eventData && eventData.grid && eventData.grid.getId() === 'GRID_ID') {
					updateGridCells(eventData.grid);
				}
			});
		}
	});
})();
