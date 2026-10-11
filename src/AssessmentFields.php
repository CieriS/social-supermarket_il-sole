<?php

/*
    The "griglia di valutazione" section of the application form: the lines of
    the grid that cannot be worked out from the rest of the form and are left
    to the operator. Shared by the new and the update application pages.

    Pure: returns the markup, prints nothing.
*/

declare(strict_types=1);

namespace IlSole\Form;

const MAX_HOUSEHOLD = 22;

function numberField(string $name, int $value): string
{
    return "<input type='number' name='$name' min='0' max='" . MAX_HOUSEHOLD . "' value='$value' required/>";
}

/* "No" plus every value of the range; a stored value outside the range falls back to "No" */
function rangeField(string $name, int $value, int $min, int $max): string
{
    $options = "<option value='0'>No</option>";
    for ($points = $min; $points <= $max; $points++) {
        $selected = $points === $value ? ' selected' : '';
        $options .= "<option value='$points'$selected>$points</option>";
    }
    return "<select name='$name'>$options</select>";
}

/*
    $assessment: the values to show, as returned by assessmentFromForm() or read
    from ValutazioneGriglia. $rules: the configured points, for the two ranges.
*/
function assessmentFieldsHtml(array $assessment, array $rules): string
{
    $assessment = assessmentFromForm($assessment);
    $rows = [
        'Adulti con invalidit&agrave; totale o malattia con inabilit&agrave; lavorativa'
            => numberField('InvaliditaTotaleAdulti', $assessment['InvaliditaTotaleAdulti']),
        'Adulti con invalidit&agrave; grave o malattia'
            => numberField('InvaliditaGraveAdulti', $assessment['InvaliditaGraveAdulti']),
        'Minori con invalidit&agrave; grave'
            => numberField('InvaliditaGraveMinori', $assessment['InvaliditaGraveMinori']),
        'Altri debiti di varia natura pubblica, documentabili (punti)'
            => rangeField('AltriDebiti', $assessment['AltriDebiti'], $rules['debt_other_min'], $rules['debt_other_max']),
        'Contributi e sgravi (punti da dedurre)'
            => rangeField('AltriContributi', $assessment['AltriContributi'], $rules['benefit_other_min'], $rules['benefit_other_max']),
    ];

    $html = "<h3><span Class=\"underline\">GRIGLIA DI VALUTAZIONE</span></h3>\n<table Class=\"Sep\" border='1'>\n";
    foreach ($rows as $label => $field) {
        $html .= "<tr><td Class='AlignLeft'>$label</td><td>$field</td></tr>\n";
    }
    return $html . "</table>\n";
}
