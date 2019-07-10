<?php
return [
    'general' => [
        'title' => 'Основная информация',
        'fields' => [
            'type' => 'Тип учетной записи',
            'email' => 'Email',
            'password' => 'Пароль',
            'state' => 'Состояние',
            'access' => 'Уровень доступа',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ],
    ],
    'profile' => [
        'title' => 'Профиль',
        'fields' => [
            'inn' => 'ИНН',
            'cpp' => 'Кпп',
            'okpo' => 'ОКПО',
            'name' => 'Имя',
            'patronymic' => 'Отчество',
            'surname' => 'Фамилия',
            'address' => 'Адрес',
            'phone' => 'Телефон',
        ],
    ],
    'mailing' => [
        'name' => 'Название',
        'subject' => 'Тема сообщения',
        'html' => 'HTML текст',
        'access' => 'Доступ',
        'template' => 'Шаблон',
        'date_create' => 'Дата создания',
        'date_sending' => 'Дата рассылки',
    ],
];