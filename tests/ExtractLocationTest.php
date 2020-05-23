<?php
use PHPUnit\Framework\TestCase;

class ExtractLocationTest extends TestCase
{

    /**
     * @dataProvider chatDataProvider
     */
    public function testExtractLocation($message, $expected)
    {
    	$location = \YouTubeMap\extract_location_from_message($message);
        $this->assertSame($expected, $location);
    }

    public function chatDataProvider() {
    	return [
			['Hello from Northern Norway;:)', 'Northern Norway'],
			['Hello from Bangkok, Thailand', 'Bangkok, Thailand'],
			['Hi from Gothenburg, Sweden', 'Gothenburg, Sweden'],
			['Good afternoon everyone.', ''],
			['Hi from Belfast, Ireland', 'Belfast, Ireland'],
			['Watching from Ghana ♥', 'Ghana'],
			['Montreal, Canada.', 'Montreal, Canada'],
			['Hello from San Francisco.', 'San Francisco'],
			['los angeles, CA here', 'los angeles, CA'],
			['Helloooo Aaron and everyone from West Palm Beach.', 'West Palm Beach'],
			['@aaronparecki Hey from Sunshine State!', 'Sunshine State'],
			['hello from DC', 'DC'],
			['Greetings from Tokyo!', 'Tokyo'],
			['Hi from Belgium', 'Belgium'],
			['Hi from Myanmar', 'Myanmar'],
			['Brazil!', 'Brazil'],
			['@aaronparecki hi from Allen, TX. Hopefully I can stay on for a while.', 'Allen, TX'],
			['Hi from Toronto', 'Toronto'],
			['Hi, Yakov from Toronto', 'Toronto'],
			['Hi, the Netherlands', 'the Netherlands'],
			['watching from Kenya', 'Kenya'],
			['Hello from Vancouver, Canada', 'Vancouver, Canada'],
			['hideeho! from Austin, TX', 'Austin, TX'],
			['Hi from Brazil !', 'Brazil'],
			['Good evening from Switzerland ;-)', 'Switzerland'],
			['Good morning from California', 'California'],
			['huh - doesn\'t look like dve store shows your place in line', ''],
			['Checking in from Ann Arbor, MI. Go blue', 'Ann Arbor, MI'],
			['Good evening from Sweden!!!', 'Sweden'],
			['Checking I. From the UK mine arrived yesterday, had fun setting it up with Macros and MIDI control....', ''],
			['Hello from Sweden! I got my Atem mini PRO last Week. Sold my Atem just days before...', 'Sweden'],
			['Denmark', 'Denmark'],
			['Belfast, Ireland', 'Belfast, Ireland'],
			['Sean from Ireland here', 'Ireland'],
			['Hey . This is naresh from India', 'India'],
			['Hey, Dipak from florida', 'florida'],
			['Hi from Brazil!', 'Brazil'],
			['Hi from San Francisco', 'San Francisco'],
			['Good morning from NYC!', 'NYC'],
			['Hi from Oshkosh, WI', 'Oshkosh, WI'],
			['Hi, joining from Israel', 'Israel'],
			['Norway', 'Norway'],
			['Hi from Belgium', 'Belgium'],
			['Hi! Lisboa, Portugal.', 'Lisboa, Portugal'],
			['From Dallas', 'Dallas'],
			['hi from Nimbin, Australia!', 'Nimbin, Australia'],
			['Hey, Dipak from florida', 'florida'],
			['I\'m not dead yet!', ''],
			['Hi there!', ''],
			['Olympia, WA, USA', 'Olympia, WA'],
			['Maryland, USA', 'Maryland, USA'],
			['Charlottesville, Va. Surprisingly nice day.', 'Charlottesville, Va'],
		];
    }
}
