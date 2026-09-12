<?php


           //      S    C    R    A    P          C    O    D    E    !      \\
           //      S    C    R    A    P          C    O    D    E    !      \\
           //      S    C    R    A    P          C    O    D    E    !      \\
           //      S    C    R    A    P          C    O    D    E    !      \\


        /**-----------------------------------------------------------------------
         * Final structure of the modules' allocation matrix
         */
        $smart = 23;
        $module_id = 'co7707';

        $module_allocation_matrix[$module_id] =
        [
            'tas' =>
            [
                ['weight' => 35, 'ta_email' => 'gta1'],
                ['weight' => 18, 'ta_email' =>'ta1'],
                ['weight' => $smart, 'ta_email' => 'gta1']
            ]
        ];

        // echo $module_allocation_matrix[$module_id]['tas'][35];
        // echo "<br>";
        // echo $module_allocation_matrix[$module_id]['no_of_assistants'];
        //  return $module_allocation_matrix;

        // Below NOT working
        // $how = $module_allocation_matrix[$module_id]->where($key < 20);

        /**
         * END: Final structure of the modules' allocation matrix
         * -----------------------------------------------------------------------
         */



        $ta_allocation_matrix['ta_email'] =
        [
            'weekly_working_hours' => 0,
            'modules' =>
            [
                'CO0001','CO0002', 'CO0003'
            ]
        ];

        // Add new item CO0087
        $ta_allocation_matrix1['ta_email']['modules'][] = 'CO0087';

        $CO0001 = 'CO0001';

        // Get the key of item $CO0001
        $key = array_search($CO0001, $ta_allocation_matrix1['ta_email']['modules']);

        // distroy emntry with key $key
        unset($ta_allocation_matrix['haha']['modules'][$key]);


        /** -----------------------------------------------------------------------
         * Structure of modules ROLs matrix
         */
        //
        $modules_ROLs[$module_id] =
        [
            'no_of_assistants' => 4,
            'tas' =>
            [
                'ta_email' => ['weight' => 35, 'id' => 'haha'],
                'ta_email' => ['weight' => 18, 'id' => 'ta1'],
                'ta_email' => ['weight' => $smart, 'id' => 'gta2'],
            ]
        ];

        // return $modules_ROLs;

        $index = array_search('gta2', array_column($modules_ROLs[$module_id]['tas'], 'id'));

        // echo $key;

        /**
         * END: Structure of modules ROLs matrix
         * -----------------------------------------------------------------------
         */





        /** -----------------------------------------------------------------------
         * How to check if TA_x has a weight that exceeds the weeights of the TAs already in the ROL
         * if the number of returned objects is equal to no_of_assistants then all the TAs already in the ROL weigh more then the current TA
         */

         // Get current TA's weight
         $current_ta_weight =  $modules_prefs_and_ROLs[$module_choice_id]['tas'][$current_ta_email]['weight'];

        $current_ta_weight = 20;
        $fitered = array_filter(
            $module_allocation_matrix[$module_id]['tas'],
            function ($key) use($current_ta_weight) {
                return ($key > $current_ta_weight);
            }, ARRAY_FILTER_USE_KEY
        );

        // return $fitered;


        /** END:
         * How to check if TA_x has a weight that exceeds the weeights of the TAs already in the ROL
         * if the number of returned objects is equal to no_of_assistants then all the TAs already in the ROL weigh more then the current TA
         * -----------------------------------------------------------------------
         */


