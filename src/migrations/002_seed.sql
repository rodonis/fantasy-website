-- Seed: GM user + example pages
-- Default GM password: changeme_gm  (bcrypt hash below)
-- Change immediately after first login!
SET NAMES utf8mb4;

INSERT IGNORE INTO users (username, password_hash, role, display_name)
VALUES ('gm', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gm', 'Maerwyn the Loremaster');

-- Example region page
INSERT IGNORE INTO pages (slug, title, body_md, visibility, category, updated_by)
VALUES (
  'vothmir-the-drowned-coast',
  'Vothmir, the Drowned Coast',
  '**Vothmir** is a half-sunken city on the western edge of the [[Sundered Coast]], the surviving fragment of a once-mighty thalassocracy.

tags: Region, Coast, Ruin

::: map caption="Survey of the three terraces" scale="2 leagues"
pin x=28 y=45 label="Salt-Stair" cap=1
pin x=50 y=58 label="Bell-Stair" cap=1
pin x=72 y=68 label="King''s-Stair" cap=1
pin x=85 y=78 label="Lantern Reef"
:::

## Geography

Vothmir occupies a long crescent of granite reef. The old city climbs three terraces — *Salt-Stair*, *Bell-Stair*, and *King''s-Stair* — each a ring of stone houses joined by causeways now broken.

- [[The Sunken Cathedral]] — once the seat of [[Saint Aelra]]
- [[The Causeway of Tongues]] — pilgrim road in Old Vothic
- [[King''s-Stair]] — last dry terrace, holds the council-house

## History

Vothmir was raised in the high years of the Sea-Kings, three hundred winters before the Sundering. When the gods broke, the tides did not return — and then the tides returned all at once.

> We rang the bells, as had always been done, and the bells were answered — not from above, but from below.
> — Brother Cael, last sermon

::: gm GM Eyes Only · The Drowning Truth
The tide that took Vothmir was not natural. [[Saint Aelra]] was *swallowed*, alive and aware, by her own congregation. The bells answer because she still rings them, from the inside.

Any character who takes the salt-mark from a Vothmiri matron must succeed on a **Will save (DC 16)** at the next high tide or feel an unbearable pull toward the Sunken Cathedral.
:::

## Adventure Hooks

- A pilgrim has gone missing on the Causeway. Her brother offers relics of Aelra in trade for her body.
- The bell-iron from a recent burial has surfaced at [[Hold of Karn-Vael]], a hundred leagues away.
- The Council of Fishwives has drawn the same lot seven times in a row.',
  'public', 'regions', 1
);

-- Example bestiary page with Pathfinder 1e statblock
INSERT IGNORE INTO pages (slug, title, body_md, visibility, category, updated_by)
VALUES (
  'grave-sworn-reaver',
  'Grave-Sworn Reaver',
  '**Grave-Sworn Reavers** are oathbound warriors of the Last Kings, buried in their armor and bound by a vow they did not consent to.

tags: Undead, Sworn, Karn-Vael

> Their helms are sealed with their masters'' wax. To break the seal is to free the man inside — but only briefly, and rarely with thanks.
> — Maerwyn, Loremaster of the Codex

## Lore

In the years before the Sundering, the Last Kings demanded their household guard swear an oath of service "until the iron rots from the bone." Iron does not rot. Every guardsman who died since wearing the Last Kings'' livery has risen to keep marching.

## Statblock

::: statblock
name: Grave-Sworn Reaver
cr: 6
xp: 2400
type: LE Medium undead (augmented)
init: +5
senses: darkvision 60 ft.; Perception +11
aura: oathbound (30 ft., DC 16)

# Defense
AC: 21, touch 11, flat-footed 20 (+9 armor, +1 Dex, +1 shield)
HP: 76 (8d8 + 40)
Saves: Fort +6, Ref +3, Will +8
Defensive Abilities: channel resistance +4, oath-warded; DR 5/bludgeoning and good
Immune: undead traits
Weaknesses: bound by seal

# Offense
Speed: 20 ft.
Melee: +1 longsword +12/+7 (1d8+5/19-20) or rusted halberd +11/+6 (1d10+6/x3)
Ranged: heavy crossbow +7 (1d10/19-20)
Special Attacks: grave-bond strike (DC 16), keep-the-oath

# Statistics
Str: 20
Dex: 13
Con: —
Int: 9
Wis: 14
Cha: 16
BAB: +6
CMB: +11
CMD: 22
Feats: Cleave, Improved Initiative, Power Attack, Toughness, Weapon Focus (longsword)
Skills: Intimidate +14, Perception +11, Sense Motive +9
Languages: Common, Old Vothic
Gear: banded mail, heavy steel shield, +1 longsword, rusted halberd, sealed helm

# Special Abilities
**Aura of Oathbinding (Su).** Any living creature beginning its turn within 30 ft. must succeed on a Will save (DC 16) or be shaken for 1 round and unable to break a sworn promise for 1 minute.

**Grave-Bond Strike (Su).** Once per round on a critical hit, the target must make a Fort save (DC 16) or be bound to the reaver''s master''s last oath until fulfilled or *break enchantment* is cast.

**Keep-the-Oath (Ex).** If reduced to 0 hp while an oath remains unfulfilled, the reaver rises again at full HP on the next sundown within one mile.

**Bound by Seal.** A reaver whose helm is sundered (sunder DC 22) is staggered 1d4 rounds. If its true name is spoken aloud, it crumbles to dust.
:::

::: gm GM Eyes Only · The Sealed Names
Each reaver carries its bearer''s true name in the wax of its helm, readable only by candlelight to one who shares blood or has been named kin.

The party''s [[Cael of Karn-Vael]] can read the Reaver-Captain''s helm; doing so will free him — and collapse the eastern barbican.

**Yverra of the Wax** (CR 9 sorcerer, binder) maintains the list of every reaver she has set. Any freed without her sanction — she knows within one watch.
:::

## Variants

- [[Reaver-Captain]] (CR 8) — commands up to six reavers under a banner.
- [[Drowned Reaver]] (CR 7) — buried in the Lantern Reef; gains swim 30 ft. and salt-burst breath.
- [[Hollow-Helm]] (CR 4) — broken and masterless; encountered in mobs of 2d4.',
  'public', 'bestiary', 1
);

-- Example NPC / character page
INSERT IGNORE INTO pages (slug, title, body_md, visibility, category, updated_by)
VALUES (
  'maerwyn-the-loremaster',
  'Maerwyn the Loremaster',
  '**Maerwyn** is the keeper of the Codex of the [[Broken Pantheon]], last of the Ironthread scholiasts.

tags: NPC, Loremaster, Codex

::: statblock
name: Maerwyn the Loremaster
cr: 5
xp: 1600
type: N Medium humanoid (human)
init: +2
senses: Perception +12

# Defense
AC: 13, touch 12, flat-footed 11 (+1 armor, +2 Dex)
HP: 32 (7d6 + 7)
Saves: Fort +3, Ref +4, Will +9

# Offense
Speed: 30 ft.
Melee: dagger +3 (1d4-1)
Special Attacks: spells

# Statistics
Str: 8
Dex: 14
Con: 12
Int: 18
Wis: 16
Cha: 13
BAB: +3
CMB: +2
CMD: 14
Feats: Alertness, Iron Will, Scribe Scroll, Skill Focus (Knowledge: history), Toughness
Skills: Knowledge (arcana) +14, Knowledge (history) +16, Knowledge (religion) +14, Linguistics +12, Perception +12, Spellcraft +14
Languages: Common, Draconic, Dwarven, Elven, Old Vothic, Sylvan
Gear: scholar''s robes, dagger, traveling spellbook, Codex of the Broken Pantheon (reference tome)

# Special Abilities
**Lore of the Sundering (Ex).** Maerwyn may make Knowledge checks untrained and treats all Knowledge skills as class skills. Once per day she may reroll any Knowledge check and take the better result.

**Voice of Record (Su).** Maerwyn''s written words carry subtle compulsion — any promise signed before her as witness is treated as a *geas/quest* of caster level 9 for purposes of magical compulsion effects.
:::

## Background

Maerwyn survived the [[Sundering]] as a child, apprenticed to the last Ironthread library before it fell into the [[Cinder Wastes]]. She has spent forty years reconstructing what was lost.

She does not trust gods, kings, or anyone who uses the word "destiny" without flinching.

::: gm GM Eyes Only · Maerwyn''s Secret
Maerwyn is the great-granddaughter of [[Sea-King Halric]]. She knows. She has never told anyone. The Council of Fishwives in [[Vothmir the Drowned Coast|Vothmir]] would name her queen if they knew — which is precisely why she has kept it from them for twenty years.

Her spellbook contains the *true name* of the Choir-Below, which she stole from a dying cultist in the Sunken Cathedral. She has not yet decided what to do with it.
:::',
  'public', 'npcs', 1
);

-- Seed links
INSERT IGNORE INTO links (from_slug, to_slug) VALUES
  ('vothmir-the-drowned-coast', 'sundered-coast'),
  ('vothmir-the-drowned-coast', 'saint-aelra'),
  ('vothmir-the-drowned-coast', 'hold-of-karn-vael'),
  ('grave-sworn-reaver', 'karn-vael'),
  ('grave-sworn-reaver', 'pilgrims-causeway'),
  ('grave-sworn-reaver', 'cael-of-karn-vael'),
  ('maerwyn-the-loremaster', 'broken-pantheon'),
  ('maerwyn-the-loremaster', 'sundering'),
  ('maerwyn-the-loremaster', 'cinder-wastes');

-- Seed tags
INSERT IGNORE INTO tags (page_id, tag)
SELECT p.id, t.tag FROM pages p
JOIN (
  SELECT 'vothmir-the-drowned-coast' AS slug, 'Region' AS tag UNION ALL
  SELECT 'vothmir-the-drowned-coast', 'Coast'       UNION ALL
  SELECT 'vothmir-the-drowned-coast', 'Ruin'         UNION ALL
  SELECT 'grave-sworn-reaver',        'Undead'       UNION ALL
  SELECT 'grave-sworn-reaver',        'Sworn'        UNION ALL
  SELECT 'grave-sworn-reaver',        'Karn-Vael'    UNION ALL
  SELECT 'maerwyn-the-loremaster',    'NPC'          UNION ALL
  SELECT 'maerwyn-the-loremaster',    'Loremaster'
) t ON p.slug = t.slug;
