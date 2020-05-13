

var moduleDivCounter = 0;
// Contains IDs of the programming module choices hidden divs in both preferences submission pages
let moduleDivs = [
    'module_4_div',
    'module_5_div',
    'module_6_div',
    'module_7_div',
    'module_8_div',
    'module_9_div',
    'module_10_div',
]
function showModuleDivs()
{
    document.getElementById(moduleDivs[moduleDivCounter]).style.display = 'block';
    if(moduleDivCounter == 6) document.getElementById('mod_btn').style.display = 'none';

    moduleDivCounter++;
}


var languageDivCounter = 0;
// Contains IDs of the programming language choices hidden divs in both preferences submission pages
let langDivs = [
    'language_4_div',
    'language_5_div',
    'language_6_div',
    'language_7_div',
]
function showLangDivs()
{
    document.getElementById(langDivs[languageDivCounter]).style.display = 'block';
    if(languageDivCounter == 3) document.getElementById('lang_btn').style.display = 'none';

    languageDivCounter++;
}
