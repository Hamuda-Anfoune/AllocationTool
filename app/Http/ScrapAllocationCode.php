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



        /** -----------------------------------------------------------------------
         * Structure of modules ROLs matrix
         */
        //
        $modules_ROLs[$module_id] =
        [
            'no_of_assistants' => 4,
            'tas' =>
            [
                ['weight' => 35, 'id' => 'haha'],
                ['weight' => 18, 'id' => 'ta1'],
                ['weight' => $smart, 'id' => 'gta2'],
            ]
        ];

        // return $modules_ROLs;

        $index = array_search('gta2', array_column($modules_ROLs[$module_id]['tas'], 'id'));

        // echo $key;

        /**
         * END: Structure of modules ROLs matrix
         * -----------------------------------------------------------------------
         */



        $index = array_search('CO7102', array_column($ta['modules'], 'module_id'));

        // Check if module has submitted prefs
        if(array_key_exists($module_choice_id, $modules_prefs_and_ROLs))
        {
            // Check if TA exists in the module's ROL: True if it exists, False if not
            if(array_key_exists($current_ta_email, $modules_prefs_and_ROLs[$module_choice_id]['tas']))
            {
                // Get current TA's priority
            }

            $no_of_assistants =  count($modules_prefs_and_ROLs[$module_choice_id]['tas']);
        }else{
            echo 'this module does not exist';
        }

        // check if module is has an allocation
        if(array_key_exists($module_choice_id, $module_allocation_matrix))
        {
            //
        }
        else
        {
            //
        }
        //Check if TA exists in module ROL
        // $ta_exists_in_Rol = (array_key_exists($ta['ta_email'], $modules_prefs_and_ROLs[$module_choice_id]['tas'])) ? true : false;

        // echo $ta_exists_in_Rol;


        /** -----------------------------------------------------------------------
         * How to check if TA_x has a weight that exceeds the weeights of the TAs already in the ROL
         * if the number of returned objects is equal to no_of_assistants then all the TAs already in the ROL weigh more then the current TA
         */
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


