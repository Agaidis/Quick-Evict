<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PDFEditController extends Controller
{
    public function globalHtmlAttributes ($pdfHtml, $courtDetails, $plaintiffAddress, $defendantAddress, $signature, $evictionData) {

        $pdfHtml = str_replace('__str-upper-county__', strtoupper($courtDetails->county), $pdfHtml);
        $pdfHtml = str_replace('__court-number__', $courtDetails->court_number, $pdfHtml);
        $pdfHtml = str_replace('__mdj-name__', $courtDetails->mdj_name, $pdfHtml);
        $pdfHtml = str_replace('__plaintiff-name__', $evictionData->plantiff_name, $pdfHtml);
        $pdfHtml = str_replace('__plaintiff-address__', $plaintiffAddress, $pdfHtml);
        $pdfHtml = str_replace('__defendant-address__', $defendantAddress, $pdfHtml);
        $pdfHtml = str_replace('__court-address-one__', $evictionData->court_address_line_1, $pdfHtml);
        $pdfHtml = str_replace('__court-address-two__', $evictionData->court_address_line_2, $pdfHtml);
        $pdfHtml = str_replace('__phone-number__', $courtDetails->phone_number, $pdfHtml);
        $pdfHtml = str_replace('__date__', date("m/d/Y"), $pdfHtml);
        $pdfHtml = str_replace('__signature__', $signature, $pdfHtml);
        $pdfHtml = str_replace('__eviction-id__', $evictionData->id, $pdfHtml);
        $pdfHtml = str_replace('__filing-fee__', $evictionData->filing_fee, $pdfHtml);

        return $pdfHtml;
    }

    public function localOOPAttributes ($pdfHtml, $evictionData, $defendantAddress2) {

        $pdfHtml = str_replace('__docket-number__', $evictionData->docket_number, $pdfHtml);
        $pdfHtml = str_replace('__attorney-fees__', $evictionData->attorney_fees, $pdfHtml);
        $pdfHtml = str_replace('__defendant-address-2__', $defendantAddress2, $pdfHtml);
        $pdfHtml = str_replace('__judgment-amount__', $evictionData->judgment_amount, $pdfHtml);
        $pdfHtml = str_replace('__cost-this-proceeding__', $evictionData->cost_this_proceeding, $pdfHtml);
        $pdfHtml = str_replace('__cost-original-lt-proceeding__', $evictionData->costs_original_lt_proceeding, $pdfHtml);
        $pdfHtml = str_replace('__total-fees__', $evictionData->total_judgement, $pdfHtml);

        return $pdfHtml;
    }

    public function localCivilAttributes ($pdfHtml, $evictionData) {

        $pdfHtml = str_replace('__claim-description__', $evictionData->claim_description, $pdfHtml);
        $pdfHtml = str_replace('__total-fees__', '', $pdfHtml);

        return $pdfHtml;
    }

    public function localLTCAttributes ($pdfHtml, $evictionData) {
        $pdfHtml = str_replace('[due-rent]', $evictionData->due_rent, $pdfHtml);
        $pdfHtml = str_replace('[damage-amt]', $evictionData->damage_amt, $pdfHtml);
        $pdfHtml = str_replace('[unjust-damages]', $evictionData->unjust_damages, $pdfHtml);
        $pdfHtml = str_replace('[additional-rent-amt]', $evictionData->additional_rent_amt, $pdfHtml);
        $pdfHtml = str_replace('__security-deposit__', $evictionData->security_deposit, $pdfHtml);
        $pdfHtml = str_replace('__monthly-rent__', $evictionData->monthly_rent, $pdfHtml);
        $pdfHtml = str_replace('__breached-details__', $evictionData->breached_details, $pdfHtml);
        $pdfHtml = str_replace('__property-damage-details__', $evictionData->property_damage_details, $pdfHtml);
        $pdfHtml = str_replace('__verify-name__', $evictionData->verify_name, $pdfHtml);
        $pdfHtml = str_replace('__attorney-fees__', $evictionData->attorney_fees, $pdfHtml);
        $pdfHtml = str_replace('__total-fees__', $evictionData->total_judgement, $pdfHtml);

        $isResidential = $evictionData->is_residential == true ? '<input type="checkbox" checked/>' : '<input type="checkbox"/>';
        $isNotResidential = $evictionData->is_residential == true ? '<input type="checkbox"/>' : '<input type="checkbox" checked/>';
        $abandonedCheckbox = $evictionData->is_abandoned == true ? '<input type="checkbox" checked/>' : '<input type="checkbox"/>';
        $amtGreaterThanZeroCheckbox = $evictionData->amt_greater_than_zero == true ? '<input type="checkbox" checked/>' : '<input type="checkbox"/>';
        $unjustDamagesCheckbox = $evictionData->unjust_damages != '' ? '<input type="checkbox" checked/>' : '<input type="checkbox"/>';
        $determinationRequestCheckbox = $evictionData->is_determination_request == true ? '<input type="checkbox" checked/>' : '<input type="checkbox"/>';
        $additionalRentCheckbox = $evictionData->is_additional_rent == true ? '<input type="checkbox" checked/>' : '<input type="checkbox"/>';
        $damageAmtCheckbox = $evictionData->damage_amt != '' ? '<input type="checkbox" checked/>' : '<input type="checkbox"/>';
        $attorneyFeesCheckbox = $evictionData->attorney_fees > 0 ? '<input type="checkbox" checked/>' : '<input type="checkbox"/>';
        $noQuitNotice = $evictionData->no_quit_notice == true ? '<input type="checkbox" checked/>' : '<input type="checkbox"/>';
        $quitNoticeGiven = $evictionData->no_quit_notice == true ? '<input type="checkbox"/>' : '<input type="checkbox" checked/>';
        $unsatisfiedLease = $evictionData->unsatisfied_lease == true ? '<input type="checkbox" checked/>' : '<input type="checkbox"/>';
        $breachedConditionsLease = $evictionData->breached_conditions_lease == true ? '<input type="checkbox" checked/>' : '<input type="checkbox"/>';
        $leaseEnded = $evictionData->lease_ended == true ? '<input type="checkbox" checked/>' : '<input type="checkbox"/>';

        $pdfHtml = str_replace('__is-residential__', $isResidential, $pdfHtml);
        $pdfHtml = str_replace('__is-not-residential__', $isNotResidential, $pdfHtml);
        $pdfHtml = str_replace('__abandoned-checkbox__', $abandonedCheckbox, $pdfHtml);
        $pdfHtml = str_replace('__determination-request-checkbox__', $determinationRequestCheckbox, $pdfHtml);
        $pdfHtml = str_replace('__unjust-damages-checkbox__', $unjustDamagesCheckbox, $pdfHtml);
        $pdfHtml = str_replace('__amt-greater-than-zero-checkbox__', $amtGreaterThanZeroCheckbox, $pdfHtml);
        $pdfHtml = str_replace('__additional-rent-checkbox__', $additionalRentCheckbox, $pdfHtml);
        $pdfHtml = str_replace('__damage-amt-checkbox__', $damageAmtCheckbox, $pdfHtml);
        $pdfHtml = str_replace('__attorney-fees-checkbox__', $attorneyFeesCheckbox, $pdfHtml);
        $pdfHtml = str_replace('__no-quit-notice__', $noQuitNotice, $pdfHtml);
        $pdfHtml = str_replace('__quit-notice-given__', $quitNoticeGiven, $pdfHtml);
        $pdfHtml = str_replace('__unsatisfied-lease__', $unsatisfiedLease, $pdfHtml);
        $pdfHtml = str_replace('__breached-conditions-lease__', $breachedConditionsLease, $pdfHtml);
        $pdfHtml = str_replace('__lease-ended__', $leaseEnded, $pdfHtml);

        return $pdfHtml;
    }

    public function  addSampleWatermark ($pdfHtml, $isSample) {
        if (!$isSample) {
            $pdfHtml = str_replace('<span style="position:absolute;left:100px;top:280px; color:#f2f2f2; font-size:120px;">PREVIEW</span>', '', $pdfHtml);
        }

        return $pdfHtml;
    }
}
