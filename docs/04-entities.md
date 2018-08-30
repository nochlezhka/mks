# Сущности проекта

## <a name="certificate">Certificate</a>

Справка

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| city | string | Город | - |
| number | string | Номер | - |
| dateFrom | date | Дата начала действия | - |
| dateTo | date | Дата окончания действия | - |
| client | [Client](#client) | Клиент | - |
| type | [CertificateType](#certificate-type) | Тип справки | - |
| document | [Document](#document) | Документ | - |

## <a name="certificate-type">CertificateType</a>

Тип справки

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| name | string | Название | - |
| downloadable | boolean | Доступен для скачивания | false |
| showPhoto | boolean | Отображать фото клиента | false |
| showDate | boolean | Отображать дату ниже ФИО сотрудника | false |
| contentHeaderLeft | text | Содержимое левого верхнего блока | - |
| contentHeaderRight | text | Содержимое правого верхнего блока | - |
| contentBodyRight | text | Содержимое среднего блока | - |
| contentFooter | text | Содержимое нижнего блока | - |

## <a name="client">Client</a>

Клиент

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| photoName | string | Название файла с фотографией | - |
| birthDate | date | Дата рождения | - |
| birthPlace | string | Место рождения | - |
| gender | integer | Пол (1 - м, 2 - ж) | - |
| firstname | string | Имя | - |
| lastname | string | Фамилия | - |
| middlename | string | Отчество | - |
| lastResidenceDistrict | [District](#district) | Место последнего проживания | - |
| lastRegistrationDistrict | [District](#district) | Место последней регистрации | - |
| fieldValues | [ClientFieldValue](#client-field-value) | Значения дополнительных полей | - |
| notes | \[[Note](#note)\] | Примечания | - |
| contracts | \[[Contract](#contract)\] | Договоры | - |
| documents | \[[Document](#document)\] | Документы | - |
| shelterHistories | \[[ShelterHistory](#shelter-history)\] | Данные о проживаниях в приюте | - |
| documentFiles | \[[DocumentFile](#document-file)\] | Загруженные файлы документов | - |
| services | \[[Service](#service)\] | Полученные услуги | - |
| certificates | \[[Certificate](#certificate)\] | Справки | - |
| generatedDocuments | \[[GeneratedDocument](#generated-document)\] | Сгенерированные документы | - |

## <a name="client-field">ClientField</a>

Дополнительное поле клиента

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| name | string | Название | - |
| code | string | Символьный код | - |
| enabled | boolean | Включено | true |
| type | integer | Тип | - |
| required | boolean | Обязательное поле | false |
| multiple | boolean | Допускается выбор нескольких вариантов | false |
| description | string | Подсказка | - |
| options | \[[ClientFieldOption](#client-field-option)\] | Поле | - |

## <a name="client-field-option">ClientFieldOption</a>

Вариант значения дополнительного поля клиента

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| name | string | Название | - |
| notSingle | boolean | true - больше 1-го значения | - |
| field | [ClientField](#client-field) | Поле | - |

## <a name="client-field-value">ClientFieldValue</a>

Значение дополнительного поля клиента

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| field | [ClientField](#client-field) | Поле | - |
| client | [Client](#client) | Клиент | - |
| text | text | Значене поля - текст | - |
| datetime | date | Значение поля - дата/время | - |
| option | [ClientFieldOption](#client-field-option) | Вариант значения (если не multiple) | - |
| options | [ClientFieldOption](#client-field-option) | Варианты значения (если не multiple) | - |
| filename | string | Имя файла для файлового поля | - |
| file | UploadedField | Значение поля - файл | - |

## <a name="contract">Contract</a>

Договор (сервисный план)

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| comment | text | Комментарий | - |
| number | string | Номер | - |
| dateFrom | date | Дата начала | - |
| dateTo | date | Дата завершения | - |
| client | [Client](#client) | Клиент | - |
| status | [ContractStatus](#contract-status) | Статус | - |
| document | [Document](#document) | Документ | - |
| items | \[[ContractItem](#contract-item)\] | Пункты | - |

## <a name="contract-item">ContractItem</a>

Пункт договора (сервисного плана)

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| comment | text | Комментарий | - |
| dateStart | date | Дата начала выполнения | - |
| date | date | Дата выполнения | - |
| contract | [Contract](#contract) | Договор | - |
| type | [ContractItemType](#contract-item-type) | Тип | - |

## <a name="contract-item-type">ContractItemType</a>

Тип пункта договора (сервисного плана)

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| name | string | Название | - |
| shortName | string | Сокращенное название | - |

## <a name="contract-status">ContractStatus</a>

Статус договора

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| name | string | Название | - |

## <a name="district">District</a>

Район региона РФ

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| name | string | Название | - |
| region | [Region](#region) | Регион | - |

## <a name="document">Document</a>

Документ

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| address | string | Адрес | - |
| city | string | Город | - |
| date | date | Дата | - |
| number | string | Номер | - |
| numberPrefix | string | Серия | - |
| registration | integer | Регистрация | - |
| issued | string | Кем и когда выдан | - |
| client | [Client](#client) | Клиент | - |
| type | [DocumentType](#document-type) | Тип | - |

## <a name="document-file">DocumentFile</a>

Загруженный файл документа

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| comment | text | Комментарий | - |
| client | [Client](#client) | Клиент | - |
| type | [DocumentType](#document-type) | Тип | - |
| filename | string | Имя файла | - |
| file | Vich\UploadableField | Файл | - |

## <a name="document-type">DocumentType</a>

Тип документа

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| name | string | Название | - |
| type | integer | Тип | - |

## <a name="generated-document">GeneratedDocument</a>

Построенный документ

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| client | [Client](#client) | Клиент | - |
| number | string | Номер | - |
| type | [GeneratedDocumentType](#generated-document-type) | Тип | - |
| startText | [GeneratedDocumentStartText](#generated-document-start-text) | Начальный текст | - |
| endText | [GeneratedDocumentEndText](#generated-document-end-text) | Конечный текст | - |
| text | text | Текст | - |
| whom | text | Для кого | - |
| signature | text | Подпись | - |

## <a name="generated-document-end-text">GeneratedDocumentEndText</a>

Вариант конечного текста построенного документа

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| name | string | Название | - |
| code | string | Код | - |
| text | text | Текст | - |


## <a name="generated-document-start-text">GeneratedDocumentStartText</a>

Вариант начального текста построенного документа

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| name | string | Название | - |
| code | string | Код | - |
| text | text | Текст | - |

## <a name="generated-document-type">GeneratedDocumentType</a>

Тип построенного документа

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| name | string | Название | - |
| code | string | Код | - |

## <a name="history">History</a>

История просмотров анкет клиентов

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| client | [Client](#client) | Клиент | - |

## <a name="history-download">HistoryDownload</a>

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| client | [Client](#client) | Клиент | - |
| user | [User](#user) | Клиент | - |
| date | date | Дата | - |
| certificateType | [CertificateType](#certificate-type) | Клиент | - |

## <a name="menu-item">MenuItem</a>

Для настройки отображения пунктов меню в анкете клиента

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| name | string | Название | - |
| code | string | Код | - |
| enabled | boolean | Включено | true |

## <a name="note">Note</a> 

Примечание

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| text | text | Текст | - |
| client | [Client](#client) | Клиент | - |
| important | boolean | Важное | true |

## <a name="notice">Notice</a>

Напоминание

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| text | text | Текст | - |
| date | date | Дата | - |
| client | [Client](#client) | Клиент | - |
| viewedBy | [User](#user) | Кем просмотрено | - |

## <a name="position">Position</a>

Должность пользователя

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| name | string | Название | - |
| users | \[[User](#user)\] | Пользователи с данной должностью | - |

## <a name="region">Region</a>

Регион РФ

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| name | string | Название | - |
| shortName | string | Сокращённое название | - |
| districts | \[[District](#district)\] | Районы | - |

## <a name="service">Service</a>

Полученная услуга

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| name | string | Название | - |
| comment | text | Комментарий | - |
| amount | integer | Сумма денег | - |
| client | [Client](#client) | Клиент | - |

## <a name="service-type">ServiceType</a>

Тип услуги

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| name | string | Название | - |
| pay | boolean | Платная | - |
| document | [Document](#document) | Документ | - |

## <a name="shelter-history">ShelterHistory</a>

Данные о проживании в приюте (договор о заселении)

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| comment | text | Комментарий | - |
| diphtheriaVaccinationDate | date | Дата прививки от дифтерии | - |
| fluorographyDate | date | Дата флюорографии | - |
| hepatitisVaccinationDate | date | Дата прививки от гепатита | - |
| typhusVaccinationDate | date | Дата прививки от тифа | - |
| dateFrom | date | Дата заселения | - |
| dateTo | date | Дата выселения | - |
| room | [ShelterRoom](#shelter-room) | Комната | - |
| client | [Client](#client) | Клиент | - |
| status | [ShelterStatus](#shelter-status) | Статус | - |
| contract | [Contract](#contract) | Договор | - |

## <a name="shelter-room">ShelterRoom</a>

Комната приюта

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| number | string | Номер | - |
| maxOccupants | integer | Максимальное кол-во жильцов | - |
| currentOccupants | integer | Текущее кол-во жильцов | - |
| comment | text | Комментарий | - |

## <a name="shelter-status">ShelterStatus</a>

Статус проживания в приюте

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| name | string | Название | - |

## <a name="user">User</a>

Сотрудник

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| firstname | string | Имя | - |
| lastname | string | Фамилия | - |
| middlename | string | Отчество | - |
| position | [Position](#position) | Должность | - |
| proxyDate | date | Дата доверенности | - |
| proxyNum | string | Номер доверенности | - |
| passport | string | Паспортные данные | - |
| viewedNotices | \[[Notice](#notice)\] | Просмотренные уведомления | - |
| viewedClients | \[[ViewedClient](#viewed-client)\] | Просмотренные анкеты клиентов | - |
| createdBy | date | Дата создания | - |
| updatedBy | date | Дата обновления | - |


## <a name="viewed-client">ViewedClient</a>

Просмотренная анкета клиента (для истории просмотров)

| Свойство | Тип  | Описание  | По умолчанию  |
|---|---|---|---|
| client | [Client](#client) | Клиент | - |
| createdBy | [User](#user) | Кем создано | - |
