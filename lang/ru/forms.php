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
        'new' => 'Новая рассылка',
        'name' => 'Название',
        'subject' => 'Тема сообщения',
        'html' => 'HTML текст',
        'access' => 'Доступ',
        'template' => 'Шаблон',
        'date_create' => 'Дата создания',
        'date_sending' => 'Дата рассылки',
    ],
    'templates' => [
        'new' => 'Новый шаблон',
        'name' => 'Название',
        'html' => 'HTML текст',
        'date_created' => 'Дата создания',
        'extend' => 'Дополнительные параметры',
    ],
];