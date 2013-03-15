<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.oxid-esales.com
 * @package lang
 * @copyright � OXID eSales AG 2003-2009
 * $Id: help_lang.php 14249 2008-11-18 09:48:58Z philipp.grashoff $
 */

/**
 * In this file, all help content displayed in eShop admin is stored.
 * 3 different types of help are stored:
 *
 *   1) Tooltips
 *      Syntax for identifier: TOOLTIP_TABNAME_INPUTNAME, e.g. TOOLTIP_ARTICLE_MAIN_OXSEARCHWORDS
 *
 *   2) Additional Information, popping up when clicking on icon
 *      Syntax for identifier: HELP_TABNAME_INPUTNAME, e.g. HELP_SHOP_CONFIG_BLBIDIRECTCROSS
 *
 *   3) Links to Manual pages
 *      Syntax for identifier: MANUAL_TABNAME, e.g. MANUAL_ARTICLE_EXTENDED
 */

$aLang =  array(

/*
 * Additional Information
 */
'HELP_SHOP_SYSTEM_BLOTHERCOUNTRYORDER'		=> 	"Diese Einstellung beeinflusst das Verhalten des OXID eShops, wenn f�r ein Land, in das Benutzer bestellen wollen, keine Versandkosten definiert sind:<br>" .
                                                "<ul><li>Wenn die Einstellung aktiv ist, erhalten diese Benutzer im Bestellprozess eine Meldung: Die Versandkosten werden ihnen nachtr�glich mitgeteilt, wenn Sie damit einverstanden ist. Sie k�nnen mit der Bestellung fortfahren.</li>" .
                                                "<li>Wenn die Option ausgeschaltet ist, k�nnen Benutzer aus L�ndern, f�r die keine Versandkosten definiert sind, nicht bestellen.</li></ul>",
'HELP_SHOP_SYSTEM_BLDISABLENAVBARS'			=>	"Wenn Sie diese Einstellung aktivieren, werden die meisten Navigationselemente im Bestellprozess ausgeblendet. Dadurch werden die Benutzer beim Bestellen nicht unn�tig abgelenkt.",
'HELP_SHOP_SYSTEM_SDEFAULTIMAGEQUALITY'		=>	"Empfehlenswerte Einstellungen sind ca. 40-80:<br>" .
                                                "<ul><li>Unterhalb von ca. 40 werden deutliche Kompressionsartefakte sichtbar, und die Bilder wirken unscharf.</li>".
                                                "<li>Oberhalb von ca. 80 kann man kaum eine Verbesserung der Bildqualit�t feststellen, w�hrend die Dateigr��e enorm zunimmt.</li></ul><br>".
                                                "Die Standardeinstellung ist 75.",


'HELP_SHOP_CONFIG_BLTOPNAVILAYOUT'			=>	"In der Kategorien-Navigation werden die Kategorien angezeigt. Die Kategorien-Navigation wird normalerweise links angezeigt. Wenn Sie diese Einstellung aktivieren, wird die Kategorien-Navigation anstatt links oben angezeigt.",
'HELP_SHOP_CONFIG_BLORDEROPTINEMAIL'		=>	"Wenn Double-Opt-In aktiviert ist, erhalten die Benutzer eine E-Mail mit einem Best�tigungs-Link, wenn sie sich f�r den Newsletter registrieren. Erst, wenn sie diesen Link besuchen, sind sie f�r den Newsletter angemeldet.<br>" .
                                                "Double-Opt-In sch�tzt vor Anmeldungen, die nicht gewollt sind. Ohne Double-Opt-In k�nnen beliebige E-Mail Adressen f�r den Newsletter angemeldet werden. Dies wird z. B. auch von Spam-Robotern gemacht. Durch Double-Opt-In kann der Besitzer der E-Mail Adresse best�tigen, dass er den Newsletter wirklich empfangen will.",
'HELP_SHOP_CONFIG_BLBIDIRECTCROSS'			=>	"Durch Crossselling k�nnen zu einem Artikel passende Artikel angeboten werden. Crossselling-Artikel werden im Shop bei <i>Kennen Sie schon?</i> angezeigt.<br>" .
                                                "Wenn z. B. einem Auto als Crossselling-Artikel Winterreifen zugeordnet sind, werden beim Auto die Winterreifen angezeigt." .
                                                "Wenn Bidirektionales Crossselling aktiviert ist, funktioniert Crossselling in beide Richtungen: bei den Winterreifen wird das Auto angezeigt.",
'HELP_SHOP_CONFIG_SICONSIZE'				=>	"Icons sind die kleinsten Bilder eines Artikels. Icons werden z. B. <br>" .
                                                "<ul><li>im Warenkorb angezeigt.</li>" .
                                                "<li>angezeigt, wenn Artikel in der rechten Leiste aufgelistet werden (z.B. bei den Aktionen <i>Top of the Shop</i> und <i>Schn�ppchen</i>.</li></ul>" .
                                                "Damit das Design des eShops nicht durch zu gro�e Icons gest�rt wird, werden zu gro�e Icons automatisch verkleinert. Die maximale Gr��e k�nnen Sie hier eingeben.<br>" ,
'HELP_SHOP_CONFIG_STHUMBNAILSIZE'			=>  "Thumbnails sind kleine Bilder eines Artikels. Thumbnails werden z. B. <br>" .
                                                "<ul><li>in Artikellisten angezeigt. Artikellisten sind z. B. Kategorieansichten (alle Artikel in einer Kategorie werden aufgelistet) und die Suchergebnisse.</li>" .
                                                "<li>in Aktionen angezeigt, die in der Mitte der Startseite angezeigt werden, z. B. <i>Die Dauerbrenner</i> und <i>Frisch eingetroffen!</i>.</li></ul>" .
                                                "Damit das Design des eShops nicht durch zu gro�e Thumbnails gest�rt wird, werden zu gro�e Thumbnails automatisch verkleinert. Die maximale Gr��e k�nnen Sie hier eingeben.",
'HELP_SHOP_CONFIG_BLSTOCKONDEFAULTMESSAGE'	=>	"Bei jedem Artikel k�nnen Sie einrichten, welche Meldung den Benutzern angezeigt wird, wenn der Artikel auf Lager ist. " .
                                                "Wenn diese Einstellung aktiv ist, wird den Benutzern auch dann eine Meldung angezeigt, wenn bei einem Artikel keine eigene Meldung hinterlegt ist. Dann die Standardmeldung &quot;sofort lieferbar&quot; angezeigt.",
'HELP_SHOP_CONFIG_BLSTOCKOFFDEFAULTMESSAGE'	=>	"Bei jedem Artikel k�nnen Sie einrichten, welche Meldung den Benutzern angezeigt wird, wenn der Artikel nicht auf Lager ist. " .
                                                "Wenn diese Einstellung aktiv ist, wird den Benutzern auch dann eine Meldung angezeigt, wenn bei einem Artikel keine eigene Meldung hinterlegt ist. Dann die Standardmeldung &quot;Dieser Artikel ist nicht auf Lager und muss erst nachbestellt werden&quot; angezeigt.",
'HELP_SHOP_CONFIG_BLOVERRIDEZEROABCPRICES'	=>	"Sie k�nnen f�r bestimmte Benutzer spezielle Preise einrichten. Dadurch k�nnen Sie bei jedem Artikel A, B, und C-Preise eingeben. Wenn Benutzer z. B. in der Benutzergruppe Preis A sind, werden ihnen die A-Preise anstatt dem normalen Artikelpreis angezeigt.<br>" .
                                                "Wenn die Einstellung aktiv ist, wird diesen Benutzern der normale Artikelpreis angezeigt, wenn f�r den Artikel kein A, B oder C-Preis vorhanden ist.<br>" .
                                                "Sie sollten diese Einstellung aktivieren, wenn Sie A,B und C-Preise verwenden: Ansonsten wird den bestimmten Benutzern ein Preis von 0,00 angezeigt, wenn kein A,B oder C-Preis hinterlegt ist.",
'HELP_SHOP_CONFIG_ASEARCHCOLS'				=>	"Hier k�nnen Sie die Datenbankfelder der Artikel eingeben, in denen gesucht wird. Geben Sie pro Zeile nur ein Datenbankfeld ein.<br>" .
                                                " Die am h�ufigsten ben�tigten Eintr�ge sind:" .
                                                "<ul><li>oxtitle = Titel (Name) der Artikel</li>" .
                                                "<li>oxshortdesc = Kurzbeschreibung der Artikel</li>" .
                                                "<li>oxsearchkeys = Suchw�rter, die bei den Artikeln eingetragen sind</li>" .
                                                "<li>oxartnum = Artikelnummern</li>" .
                                                "<li>oxtags	= Stichworte, bei den Artikeln eingetragen sind</li></ul>",
'HELP_SHOP_CONFIG_ASORTCOLS'				=>	"Hier k�nnen Sie die Datenbankfelder der Artikel eingeben, nach denen Artikellisten sortiert werden k�nnen. Geben Sie pro Zeile nur ein Datenbankfeld ein.<br>" .
                                                "Die am h�ufigsten ben�tigten Eintr�ge sind:" .
                                                "<ul><li>oxtitle = Titel (Name) der Artikel</li>" .
                                                "<li>oxprice = Preis der Artikel</li>" .
                                                "<li>oxvarminprice	= Der niedrigste Preis der Artikel, wenn Varianten mit verschiedenen Preisen verwendet werden.</li>" .
                                                "<li>oxartnum = Artikelnummern</li>" .
                                                "<li>oxrating = Die Bewertung der Artikel</li>" .
                                                "<li>oxstock = Lagerbestand der Artikel</li></ul>",
'HELP_SHOP_CONFIG_AMUSTFILLFIELDS'			=>	"Hier k�nnen Sie eingeben, welche Felder von Benutzern ausgef�llt werden m�ssen, wenn Sie sich registieren. Sie m�ssen die entsprechenden Datenbankfelder angeben. Geben Sie pro Zeile nur ein Datenbankfeld ein.<br>" .
                                                "Die am h�ufigsten ben�tigten Eintr�ge f�r die Benutzerdaten sind:" .
                                                "<ul><li>oxuser__oxfname = Vorname</li>" .
                                                "<li>oxuser__oxlname = Nachname</li>" .
                                                "<li>oxuser__oxstreet = Stra�e</li>" .
                                                "<li>oxuser__oxstreetnr = Hausnummer</li>" .
                                                "<li>oxuser__oxzip = Postleitzahl</li>" .
                                                "<li>oxuser__oxcity = Stadt</li>" .
                                                "<li>oxuser__oxcountryid = Land</li>" .
                                                "<li>oxuser__oxfon = Telefonnummer</li></ul><br>" .
                                                "Sie k�nnen auch angeben, welche Felder ausgef�llt werden m�ssen, wenn Benutzer eine Lieferadresse eingeben. Die am h�ufigsten ben�tigten Eintr�ge sind:" .
                                                "<ul><li>oxaddress__oxfname = Vorname</li>" .
                                                "<li>oxaddress__oxlname = Nachname</li>" .
                                                "<li>oxaddress__oxstreet = Stra�e</li>" .
                                                "<li>oxaddress__oxstreetnr = Strassennummer</li>" .
                                                "<li>oxaddress__oxzip = Postleitzahl</li>" .
                                                "<li>oxaddress__oxcity = Stadt</li>" .
                                                "<li>oxaddress__oxcountryid = Land</li>" .
                                                "<li>oxaddress__oxfon = Telefonnummer</li></ul>",


'HELP_SHOP_SEO_OXTITLEPREFIX'				=>	"Jede einzelne Seite hat einen Titel. Er wird im Browser als Titel des Browser-Fensters angezeigt. Mit Titel Prefix und Titel Postfix haben Sie die M�glichkeit, vor und hinter jeden Seitentitel Text einzuf�gen:<br>" .
                                                "<ul><li>Geben Sie Titel Prefix den Text ein, der vor dem Titel erscheinen soll.</li>" .
                                                "<li>Geben Sie in Titel Postfix den Text ein, der hinter dem Titel erscheinen soll.</li></ul>",
'HELP_SHOP_SEO_OXTITLESUFFIX'				=>	"Jede einzelne Seite hat einen Titel. Er wird im Browser als Titel des Browser-Fensters angezeigt. Mit Titel Prefix und Titel Postfix haben Sie die M�glichkeit, vor und hinter jeden Seitentitel Text einzuf�gen:<br>" .
                                                "<ul><li>Geben Sie Titel Prefix den Text ein, der vor dem Titel erscheinen soll.</li>" .
                                                "<li>Geben Sie in Titel Postfix den Text ein, der hinter dem Titel erscheinen soll.</li></ul>",


'HELP_SHOP_MAIN_OXPRODUCTIVE'				=>	"Wenn die Einstellung <b>nicht</b> aktiv ist, werden am unteren Ende jeder Seite Informationen zu Ladezeiten angezeigt. Au�erdem werden Debug-Informationen angezeigt. Diese Informationen sind f�r Entwickler wichtig, wenn sie den OXID eShop anpassen.<br>" .
                                                "<b>Aktivieren Sie diese Einstellung, bevor ihr eShop �ffentlich zug�nglich gemacht wird! Dadurch wird den Benutzern nur der eShop ohne die zus�tzlichen Informationen angezeigt.</b>",


'HELP_ARTICLE_STOCK_OXSTOCKFLAG'			=>	"Hier k�nnen Sie einstellen, wie sich der eShop verh�lt, wenn der Artikel ausverkauft ist:<br>" .
                                                "<ul><li>Standard: Der Artikel kann auch dann bestellt werden, wenn er ausverkauft ist.</li>" .
                                                "<li>Fremdlager: Der Artikel kann immer gekauft werden und wird immer als &quot;auf Lager&quot; angezeigt. (In einem Fremdlager kann der Lagerbestand nicht ermittelt werden. Deswegen wird der Artikel immer als �auf Lager� gef�hrt).</li>" .
                                                "<li>Wenn Ausverkauft offline: Der Artikel wird nicht angezeigt, wenn er ausverkauft ist.</li>" .
                                                "<li>Wenn Ausverkauft nicht bestellbar: Der Artikel wird angezeigt, wenn er ausverkauft ist, aber er kann nicht bestellt werden.</li></ul>",
'HELP_ARTICLE_STOCK_OXREMINDACTIV'			=>	"Hier k�nnen Sie einrichten, dass Ihnen eine E-Mail gesendet wird, sobald der der Lagerbestand unter den hier eingegebenen Wert sinkt. Dadurch werden Sie rechtzeitig informiert, wenn der Artikel fast ausverkauft ist. Setzen Sie hierzu das H�kchen und geben Sie den Bestand ein, ab dem Sie informiert werden wollen.",
'HELP_ARTICLE_STOCK_OXDELIVERY'				=>	"Hier k�nnen Sie eingeben, ab wann ein Artikel wieder lieferbar ist, wenn er ausverkauft ist. Das Format ist Jahr-Monat-Tag, z. B. 2008-10-21.",


'HELP_DELIVERY_MAIN_OXFIXED'				=>	"Mit dieser Einstellung k�nnen Sie ausw�hlen, wie oft der Preis Auf-/Abschlag berechnet wird:<br>" .
                                                "<ul><li>Einmal pro Warenkorb: Der Preis wird einmal f�r die gesamte Bestellung berechnet.</li>" .
                                                "<li>Einmal pro unterschiedlichem Artikel: Der Preis wird f�r jeden unterschiedlichen Artikel im Warenkorb einmal berechnet. Wie oft ein Artikel bestellt wird, ist dabei egal.</li>" .
                                                "<li>F�r jeden Artikel: Der Preis wird f�r jeden Artikel im Warenkorb berechnet.</li></ul>",
'HELP_DELIVERY_MAIN_OXDELTYPE'				=>	"Mit Bedingung k�nnen Sie einstellen, dass die Versandkostenregel nur f�r eine bestimmte Bedingung g�ltig ist. Sie k�nnen zwischen 4 Bedingungen w�hlen:<br>" .
                                                "<ul><li>Menge: Anzahl aller Artikel im Warenkorb.</li>" .
                                                "<li>Gr��e: Die Gesamtgr��e aller Artikel.</li>" .
                                                "<li>Gewicht: Das Gesamtgewicht der Bestellung in Kilogramm.</li>" .
                                                "<li>Preis: Der Einkaufswert der Bestellung.</li></ul>" .
                                                "Mit den Eingabefeldern <b>&gt;=</b> (gr��er gleich) und <b>&lt;=</b> (kleiner gleich) k�nnen Sie den Bereich einstellen, f�r den die Bedingung g�ltig sein soll. Bei <b>&lt;=</b> muss eine gr��ere Zahl als bei <b>&gt;=</b> eingegeben werden.",


'HELP_DELIVERYSET_MAIN_OXPOS'				=>	"Die Sortierung gibt an, in welcher Reihenfolge die Versandarten den Benutzern angezeigt werden:<br>" .
                                                "<ul><li>Die Versandart mit der niedrigsten Zahl wird ganz oben angezeigt.</li>" .
                                                "<li>Die Versandart mit der h�chsten Zahl wird ganz unten angezeigt.</li></ul>",


'HELP_PAYMENT_MAIN_OXSORT'					=>	"Die Sortierung gibt an, in welcher Reihenfolge die Zahlungsarten den Benutzern angezeigt werden:<br>" .
                                                "<ul><li>Die Zahlungsart mit der niedrigsten Sortierung wird an erster Stelle angezeigt.</li>" .
                                                "<li>Die Zahlungsart mit der h�chten Sortierung wird an letzter Stelle angezeigt.</li></ul>",
'HELP_PAYMENT_MAIN_OXFROMBONI'				=>	"Hier k�nnen Sie einstellen, dass die Zahlungsarten nur Benutzern zur Verf�gung stehen, die mindestens einen bestimmten Bonit�tsindex haben. Den Bonit�tsindex k�nnen Sie f�r jeden Benutzer unter <b><i><Benutzer verwalten -&gt; Benutzer -&gt; Erweitert</i></b> eingeben",


'HELP_CATEGORY_MAIN_OXSORT'					=>	"Mit Sortierung k�nnen Sie festlegen, in welcher Reihenfolge die Kategorien angezeigt werden: Die Kategorie mit der kleinsten Zahl wird oben angezeigt, die Kategorie mit der gr��ten Zahl unten.",
'HELP_CATEGORY_MAIN_OXPRICEFROM'			=>	"Mit Preis von/bis k�nnen Aie einstellen, dass in der Kategorie nur die zugeordneten Artikel angezeigt werden, die einen bestimmten Preis haben. Im ersten Eingabefeld wird die Untergrenze eingegeben, in das zweite Eingabefeld die Obergrenze.<br>" .
                                                "<b>Wenn alle zugeordneten Artikel angezeigt werden sollen, dann geben Sie in Preis von/bis nichts ein!</b>",
'HELP_CATEGORY_MAIN_OXPRICETO'				=>	"Mit Preis von/bis k�nnen Aie einstellen, dass in der Kategorie nur die zugeordneten Artikel angezeigt werden, die einen bestimmten Preis haben. Im ersten Eingabefeld wird die Untergrenze eingegeben, in das zweite Eingabefeld die Obergrenze.<br>" .
                                                "<b>Wenn alle zugeordneten Artikel angezeigt werden sollen, dann geben Sie in Preis von/bis nichts ein!</b>",
);

