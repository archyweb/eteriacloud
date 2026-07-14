<?php

$physical_path=dirname(__FILE__);

/* Auth classes */
require_once $physical_path.'/zxcvbn-php/Feedback.php';
require_once $physical_path.'/zxcvbn-php/Scorer.php';
require_once $physical_path.'/zxcvbn-php/TimeEstimator.php';
require_once $physical_path.'/zxcvbn-php/Matchers/MatchInterface.php';
require_once $physical_path.'/zxcvbn-php/Matchers/BaseMatch.php';
require_once $physical_path.'/zxcvbn-php/Matchers/Bruteforce.php';
require_once $physical_path.'/zxcvbn-php/Matchers/DateMatch.php';
require_once $physical_path.'/zxcvbn-php/Matchers/DictionaryMatch.php';
require_once $physical_path.'/zxcvbn-php/Matchers/L33tMatch.php';
require_once $physical_path.'/zxcvbn-php/Matchers/RepeatMatch.php';
require_once $physical_path.'/zxcvbn-php/Matchers/ReverseDictionaryMatch.php';
require_once $physical_path.'/zxcvbn-php/Matchers/SequenceMatch.php';
require_once $physical_path.'/zxcvbn-php/Matchers/SpatialMatch.php';
require_once $physical_path.'/zxcvbn-php/Matchers/YearMatch.php';
require_once $physical_path.'/zxcvbn-php/Matcher.php';
require_once $physical_path.'/zxcvbn-php/Zxcvbn.php';
// PHPAuth legacy classes moved to private/_common/libs/PHPAuth.
// require_once $physical_path.'/PHPAuth/Config.php';
// require_once $physical_path.'/PHPAuth/Auth.php';
require_once $physical_path.'/../libs/PHPAuth/vendor/autoload.php';
require_once $physical_path.'/PHPMailer/src/PHPMailer.php';
require_once $physical_path.'/PHPMailer/src/SMTP.php';
require_once $physical_path.'/PHPMailer/src/Exception.php';
require_once $physical_path.'/ArchyPHPAuthMailer.php';
require_once $physical_path.'/ArchyPHPAuth.php';
require_once $physical_path.'/Aws/aws-autoloader.php';
require_once $physical_path.'/PHPOffice/vendor/autoload.php';


/* related classes */
require_once $physical_path.'/ExtraActions.php';
require_once $physical_path.'/AppActions.php';
/*if(isset($subdomain_root) and is_file($subdomain_root.'/overrides/CustomActions.php')):*/
	require_once $physical_path.'/CustomActions.php';
/*endif;*/
require_once $physical_path.'/App.php';
/* related classes */

require_once $physical_path.'/Invoice.php';
require_once $physical_path.'/ACube.php';
require_once $physical_path.'/Receipt.php';
require_once $physical_path.'/PassiveReceipt.php';
require_once $physical_path.'/PettyCash.php';
require_once $physical_path.'/CashBudget.php';
require_once $physical_path.'/Message.php';
require_once $physical_path.'/Membership.php';
require_once $physical_path.'/Report.php';
require_once $physical_path.'/Calendar.php';
require_once $physical_path.'/Budget.php';
require_once $physical_path.'/Notice.php';
require_once $physical_path.'/Firm.php';
require_once $physical_path.'/City.php';
require_once $physical_path.'/Registry.php';
require_once $physical_path.'/RegistryCategory.php';
require_once $physical_path.'/Quote.php';
require_once $physical_path.'/CreditNote.php';
require_once $physical_path.'/Support.php';
require_once $physical_path.'/Event.php';
require_once $physical_path.'/Queue.php';
require_once $physical_path.'/ServiceMembership.php';
require_once $physical_path.'/ManagePdf.php';
require_once $physical_path.'/Reminder.php';
require_once $physical_path.'/Placeholder.php';
require_once $physical_path.'/Model.php';
require_once $physical_path.'/StoreCloud.php';
require_once $physical_path.'/StoreManager.php';
require_once $physical_path.'/StoreR2Adapter.php';
require_once $physical_path.'/StoreLocalAdapter.php';
require_once $physical_path.'/StoreFileRepository.php';
require_once $physical_path.'/Api.php';
require_once $physical_path.'/StatsCloudStorageService.php';
require_once $physical_path.'/CoreUpdate.php';
require_once $physical_path.'/AdditionalFields.php';
require_once $physical_path.'/ArchyCore/Schema/FieldSearchResolver.php';
?>
