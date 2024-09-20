BX.namespace("GetDealAutoById");

// Popup окно для просмотра сделок по автомобилю
BX.GetDealAutoById = {
	DealPopup: {
		openPopup: function (autoId, model) {
			BX.ajax.runComponentAction('otusdev:client.auto.manager', 'getDealAutoById', {
				mode: 'class',
				data: {
					post: {
						autoId: autoId,
					}
				}
			}).then(function (response) {
				if (response.status === 'success') {
					const deals = response.data;

					let contentHtml = ``;
					deals.forEach(deal => {
						contentHtml += `
                            <div class="bx-popup-deal">
                                <h4 class="bx-popup-deal-title">Сделка: ${deal.TITLE}</h4>
                                <p class="bx-popup-deal-date">Дата создания: ${deal.DATE_CREATE}</p>
                                <table class="bx-popup-product-table">
                                    <thead>
                                        <tr>
                                            <th>Товар</th>
                                            <th>Количество</th>
                                            <th>Цена</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${deal.PRODUCTS.map(product => `
                                            <tr>
                                                <td>${product.NAME}</td>
                                                <td>${product.QUANTITY}</td>
                                                <td>${product.PRICE} руб.</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;
					});

					const popup = BX.PopupWindowManager.create("popup-message-" + autoId, null, {
						content: contentHtml,
						width: 600,
						height: 'auto',
						zIndex: 100,
						className: 'garage-popup bx-popup',
						closeIcon: {opacity: 1},
						titleBar: `Сделки по автомобилю: ${model}`,
						closeByEsc: true,
						autoHide: false,
						draggable: false,
						resizable: true,
						lightShadow: true,
						overlay: {backgroundColor: 'black', opacity: 500},
						events: {
							onPopupShow: function () {
							},
							onPopupClose: function () {
							}
						}
					});

					popup.show();
				}
			});
		}
	}
}
