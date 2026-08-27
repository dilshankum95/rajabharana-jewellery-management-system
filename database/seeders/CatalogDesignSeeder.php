<?php

namespace Database\Seeders;

use App\Enums\AvailabilityStatus;
use App\Models\CatalogDesign;
use Illuminate\Database\Seeder;

class CatalogDesignSeeder extends Seeder
{
    public function run(): void
    {
        CatalogDesign::where('code', 'like', 'CAT-%')->delete();

        $catalog = [
            'ring' => [
                ['Classic Temple Ring', 'Traditional Sri Lankan temple-inspired ring with intricate filigree work.', 8.5, 185000],
                ['Lotus Bloom Ring', 'Delicate lotus motif band crafted in polished 22K gold.', 6.2, 142000],
                ['Heritage Signet Ring', 'Bold signet style with engraved Rajabharana emblem.', 9.8, 210000],
                ['Bridal Solitaire Ring', 'Elegant solitaire setting with a refined gold band.', 5.5, 165000],
                ['Vintage Filigree Ring', 'Open-work filigree pattern inspired by Kandyan artistry.', 7.1, 178000],
                ['Minimal Band Ring', 'Sleek daily-wear band with a mirror finish.', 4.2, 98000],
                ['Ruby Accent Ring', 'Central ruby accent set in a warm gold halo.', 8.0, 225000],
                ['Twist Shank Ring', 'Interwoven dual-band design for a modern look.', 6.8, 155000],
                ['Pearl Cluster Ring', 'Cluster of seed pearls arranged in a floral pattern.', 7.5, 192000],
                ['Royal Crest Ring', 'Statement ring featuring a raised crest motif.', 10.2, 245000],
            ],
            'necklace' => [
                ['Bridal Necklace', 'Grand bridal necklace with ruby and emerald accents.', 45.0, 890000],
                ['Temple Collar Necklace', 'Wide collar necklace with traditional temple motifs.', 38.5, 760000],
                ['Layered Chain Necklace', 'Three-tier layered chains for an elegant drape.', 22.0, 420000],
                ['Ruby Drop Necklace', 'Pendant drop necklace with a faceted ruby centerpiece.', 18.5, 385000],
                ['Minimal Bar Necklace', 'Horizontal bar pendant on a fine gold chain.', 8.2, 165000],
                ['Floral Garland Necklace', 'Garland-style necklace with repeating floral links.', 32.0, 620000],
                ['Heritage Choker', 'Close-fitting choker with engraved heritage patterns.', 28.0, 540000],
                ['Pearl Strand Necklace', 'Graduated pearl strand with a gold clasp.', 15.0, 310000],
                ['Coin Necklace', 'Lakshmi coin motif necklace for festive occasions.', 35.5, 680000],
                ['Contemporary Y-Necklace', 'Modern Y-drop silhouette with a polished finish.', 20.5, 395000],
            ],
            'bracelet' => [
                ['Cobra Link Bracelet', 'Flexible cobra-link bracelet with a secure clasp.', 14.5, 285000],
                ['Tennis Bracelet', 'Continuous line of handset stones in gold links.', 12.0, 340000],
                ['Charm Bracelet', 'Five removable charms on a sturdy gold chain.', 11.2, 265000],
                ['Bangle-Style Bracelet', 'Rigid cuff-inspired bracelet with hinge closure.', 18.0, 355000],
                ['Figaro Chain Bracelet', 'Classic figaro link pattern for everyday wear.', 9.5, 185000],
                ['Heritage Engraved Bracelet', 'Flat bracelet with traditional scroll engraving.', 16.5, 320000],
                ['Pearl Line Bracelet', 'Alternating pearls and gold beads.', 10.8, 248000],
                ['Minimal Cuff Bracelet', 'Open cuff with a satin-brushed finish.', 13.0, 255000],
                ['Floral Panel Bracelet', 'Linked panels with raised floral detailing.', 15.5, 298000],
                ['Royal Link Bracelet', 'Heavy interlocking links with a box clasp.', 20.0, 410000],
            ],
            'earring' => [
                ['Floral Drop Earrings', 'Delicate floral drops with pearl detailing.', 4.5, 78000],
                ['Temple Stud Earrings', 'Compact studs with miniature temple motifs.', 3.2, 62000],
                ['Chandelier Earrings', 'Multi-tier chandelier design for celebrations.', 8.5, 145000],
                ['Hoop Earrings', 'Classic medium hoops with a high polish.', 5.0, 88000],
                ['Jhumka Earrings', 'Traditional jhumka bells with filigree caps.', 7.8, 132000],
                ['Pearl Stud Earrings', 'Round pearl studs in a gold bezel setting.', 2.8, 55000],
                ['Threader Earrings', 'Long threader chains with gold bar ends.', 3.5, 68000],
                ['Cluster Earrings', 'Dense gemstone cluster in a dome shape.', 6.2, 118000],
                ['Leaf Motif Earrings', 'Asymmetric leaf shapes with a matte finish.', 4.0, 72000],
                ['Bridal Dangler Earrings', 'Statement bridal danglers with layered tiers.', 9.5, 168000],
            ],
            'chain' => [
                ['Minimalist Chain', 'Sleek daily-wear chain available in multiple lengths.', 6.0, 95000],
                ['Singapore Chain', 'Diamond-cut Singapore link chain with bright sparkle.', 8.5, 128000],
                ['Rope Chain', 'Twisted rope pattern chain with substantial weight.', 12.0, 185000],
                ['Box Chain', 'Square-link box chain for pendant pairing.', 7.2, 112000],
                ['Figaro Chain', 'Classic figaro alternating link chain.', 9.0, 138000],
                ['Cable Chain', 'Simple cable links with a lobster clasp.', 5.5, 88000],
                ['Wheat Chain', 'Interwoven wheat pattern with a smooth drape.', 10.5, 162000],
                ['Snake Chain', 'Flexible snake chain with a seamless look.', 6.8, 105000],
                ['Anchor Chain', 'Nautical anchor links with a bold profile.', 11.0, 172000],
                ['Paperclip Chain', 'Modern elongated paperclip link chain.', 8.0, 125000],
            ],
            'pendant' => [
                ['Royal Pendant Set', 'Elegant pendant with matching ear studs for special occasions.', 15.0, 320000],
                ['Om Pendant', 'Sacred Om symbol pendant with brushed detailing.', 4.5, 85000],
                ['Heart Locket Pendant', 'Engraved heart locket that opens for a photo.', 6.8, 125000],
                ['Cross Pendant', 'Slim cross pendant with a polished mirror finish.', 3.5, 68000],
                ['Initial Pendant', 'Customizable initial block pendant design.', 4.0, 75000],
                ['Gemstone Solitaire Pendant', 'Single stone solitaire in a four-prong setting.', 5.2, 142000],
                ['Lakshmi Coin Pendant', 'Lakshmi coin motif framed in a gold bezel.', 7.5, 155000],
                ['Teardrop Pendant', 'Smooth teardrop silhouette on a hidden bail.', 4.8, 92000],
                ['Floral Medallion Pendant', 'Circular medallion with raised floral relief.', 6.0, 118000],
                ['Bar Pendant', 'Vertical bar pendant with a satin stripe detail.', 3.8, 72000],
            ],
            'bangle' => [
                ['Heritage Bangle', 'Handcrafted bangle featuring traditional Rajabharana motifs.', 25.0, 450000],
                ['Plain Gold Bangle', 'Classic round bangle with a high-polish finish.', 20.0, 380000],
                ['Screw Lock Bangle', 'Hollow bangle with a secure screw-lock mechanism.', 18.5, 355000],
                ['Kada Bangle', 'Bold kada style with a hinge and safety clasp.', 30.0, 520000],
                ['Filigree Bangle', 'Open filigree work with a lightweight feel.', 16.0, 310000],
                ['Engraved Cuff Bangle', 'Wide cuff with hand-engraved scroll patterns.', 22.5, 410000],
                ['Twisted Bangle', 'Two-tone twisted wire bangle design.', 14.0, 275000],
                ['Stone Set Bangle', 'Channel-set stones around the full circumference.', 24.0, 485000],
                ['Matte Finish Bangle', 'Contemporary bangle with a satin matte surface.', 19.0, 365000],
                ['Pair Thin Bangles', 'Set of two slim stacking bangles sold together.', 12.0, 228000],
            ],
            'anklet' => [
                ['Delicate Anklet', 'Fine chain anklet with a tiny bell charm.', 5.5, 88000],
                ['Bead Anklet', 'Alternating gold beads on a flexible chain.', 6.2, 95000],
                ['Figaro Anklet', 'Short figaro link anklet with adjustable extender.', 7.0, 102000],
                ['Charm Anklet', 'Three miniature charms on a slim gold chain.', 5.8, 92000],
                ['Layered Anklet', 'Double-strand layered anklet with a spring clasp.', 8.0, 115000],
                ['Coin Anklet', 'Small Lakshmi coins spaced along the chain.', 9.5, 138000],
                ['Pearl Anklet', 'Freshwater pearls interspersed with gold links.', 6.5, 98000],
                ['Snake Anklet', 'Smooth snake chain anklet for everyday wear.', 5.0, 82000],
                ['Heart Anklet', 'Repeating heart links with a polished shine.', 6.8, 99000],
                ['Adjustable Anklet', 'Adjustable length anklet with a slider clasp.', 4.8, 78000],
            ],
            'other' => [
                ['Gold Nose Pin', 'Traditional nose pin with a secure screw back.', 1.2, 28000],
                ['Waist Belt Ornament', 'Decorative gold waist ornament for bridal wear.', 35.0, 620000],
                ['Toe Ring Pair', 'Pair of adjustable toe rings with a floral cut.', 2.5, 45000],
                ['Brooch Pin', 'Vintage-style brooch with a safety pin clasp.', 8.0, 155000],
                ['Hair Pin Set', 'Set of two gold hair pins with pearl tips.', 4.5, 88000],
                ['Gold Cufflinks', 'Engraved square cufflinks with a toggle back.', 6.0, 125000],
                ['Tie Pin', 'Slim tie pin with a polished gold bar.', 2.0, 42000],
                ['Baby Bangle', 'Small christening bangle with rounded edges.', 5.5, 98000],
                ['Gold Bookmark', 'Filigree bookmark with a tassel ring top.', 3.5, 65000],
                ['Commemorative Medal', 'Custom commemorative gold medal with ribbon loop.', 10.0, 185000],
            ],
        ];

        $prefixes = [
            'ring' => 'R',
            'necklace' => 'N',
            'bracelet' => 'BR',
            'earring' => 'E',
            'chain' => 'C',
            'pendant' => 'P',
            'bangle' => 'B',
            'anklet' => 'A',
            'other' => 'O',
        ];

        $goldQualities = ['22k', '22k', '22k', '18k', '18k', '24k', '22k', '18k', '22k', '22k'];

        foreach ($catalog as $category => $designs) {
            $prefix = $prefixes[$category] ?? strtoupper(substr($category, 0, 2));

            foreach ($designs as $index => [$name, $description, $weight, $price]) {
                $number = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);

                CatalogDesign::updateOrCreate(
                    ['code' => "SEED-{$prefix}{$number}"],
                    [
                        'name' => $name,
                        'description' => $description,
                        'category' => $category,
                        'gold_quality' => $goldQualities[$index],
                        'weight_grams' => $weight,
                        'selling_price' => $price,
                        'availability_status' => AvailabilityStatus::Available,
                        'stock_quantity' => 3,
                    ]
                );
            }
        }
    }
}
