<?php

return [
    'aryStatus' => [
        '1' => 'Active',
        '0' => 'Inactive',
    ],
    'coverageOptions' => [
        "NDIS",
        "Aged Care",
        "Medicare",
        "Privately",
        "Insurance",
        "Other"
    ],
    'deliveryService' => [
        "In Clinic",
        "At home",
        "Telehealth",
        "Other"
    ],
    'ndisManagementOptions' => [
        [
            'id' => 'ndis_managed',
            'value' => 'NDIA managed',
            'label' => 'Is your plan NDIA managed?'
        ],
        [
            'id' => 'plan_managed',
            'value' => 'Plan managed',
            'label' => 'Plan managed'
        ],
        [
            'id' => 'self_managed',
            'value' => 'Self managed',
            'label' => 'Self managed?'
        ]
    ]
];