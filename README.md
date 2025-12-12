# FLUX COMPLET - FACTURATION ET RAPPORTS STATISTIQUES DGI

## Guide Technique Complet - De la Création de Facture aux Rapports DGI

---

## ÉTAPE 1 : CRÉATION D'UNE FACTURE

### 1.1. Accès au Système
- Ouvrez le menu de gauche dans l'ERP MALABAR
- Déroulez jusqu'à l'option **Invoicing**
- Choisissez le type de transaction : **Import** ou **Export**

### 1.2. Sélection du Client
- Sélectionnez le client dans la liste déroulante
- Choisissez l'option **Create New Invoice**

### 1.3. Remplissage des Informations
- Choisissez la licence commerciale
- Remplissez tous les champs obligatoires :
  - Date de la facture
  - Devises (USD ou CDF)
  - Taux de change si applicable
  - Mode de paiement (Espèces, Virement, Mobile Money, etc.)
  - Conditions de paiement

### 1.4. Ajout des Articles/Services
- Cliquez sur **Add Item** pour ajouter chaque article ou service
- Pour chaque ligne, renseignez :
  - Code de l'article/service
  - Dénomination
  - Quantité
  - Prix unitaire
  - TVA applicable (0% ou 16%)
  - Groupe de taxation (A pour exonéré, B pour taxable)

### 1.5. Validation de la Facture
- Vérifiez tous les montants calculés :
  - Montant Hors Taxes (HT)
  - Montant TVA
  - Montant Toutes Taxes Comprises (TTC)
- Cliquez sur **Submit** pour enregistrer la facture

**Résultat** : La facture est créée avec un statut **En Attente** (Pending). Elle n'est pas encore normalisée DGI.

---

## ÉTAPE 2 : VALIDATION DE LA FACTURE

### 2.1. Accès aux Factures
- Dans le menu **Invoicing**, choisissez **View Invoices**
- Sélectionnez le même type (Import ou Export)
- Sélectionnez le même client

### 2.2. Tableau des Factures en Attente
- Vous voyez toutes les factures avec leurs statuts
- Les factures **En Attente** doivent être validées avant normalisation

### 2.3. Validation
- Localisez la facture à valider
- Cliquez sur le bouton **Valider** ou **Approve**
- Confirmez la validation

**Résultat** : La facture passe au statut **Validée** et peut maintenant être normalisée DGI.

---

## ÉTAPE 3 : NORMALISATION DGI (FACTURE DE VENTE - FV)

### 3.1. Accès au Bouton de Normalisation
- Dans le tableau **View Invoices**
- Section des factures validées (non encore normalisées)
- Localisez le bouton **FV DGI** ou **INVOICE DGI**

### 3.2. Lancement de la Normalisation
- Cliquez sur le bouton **FV DGI**
- Une fenêtre de confirmation apparaît :
  ```
  Voulez-vous normaliser cette facture auprès de la DGI ?
  Cette action est irréversible.

  [Annuler]  [Oui, Normaliser]
  ```
- Cliquez sur **Oui, Normaliser**

### 3.3. Processus de Normalisation
Le système effectue automatiquement :

1. **Récupération des données** de la facture depuis la base de données
2. **Préparation de la requête** JSON pour l'API DGI :
   ```json
   {
     "type": "FV",
     "items": [...],
     "customer": {...},
     "payment": {...}
   }
   ```
3. **Envoi à l'API DGI** via POST /api/invoice
4. **Confirmation automatique** via PUT /api/invoice/{uid}/confirm
5. **Réception de la réponse** contenant :
   - **UID** : Identifiant unique DGI
   - **Code DEF** : Code définitif pour le QR code
   - **NIM** : Numéro interne de machine
   - **Compteur** : Numéro de compteur
   - **Date de normalisation**

### 3.4. Enregistrement dans la Base de Données
Le système met à jour la table `facture_dossier` :
```sql
UPDATE facture_dossier SET
  code_UID = '[UID reçu de la DGI]',
  code_DEF_DGI = '[Code DEF reçu]',
  date_DGI = '[Date et heure actuelle]',
  type_facture_DGI = 'FV',
  nim_DGI = '[NIM]',
  compteur_DGI = '[Compteur]',
  qrcode_string_DGI = '[Données QR]'
WHERE ref_fact = '[Référence facture]'
```

### 3.5. Confirmation Visuelle
- Un message de succès s'affiche avec :
  - UID de la facture
  - Code DEF DGI
- La page se recharge automatiquement
- La facture apparaît maintenant dans le tableau **Factures Normalisées** (Standardized)

**Résultat** : La facture est officiellement normalisée DGI avec statut **FV Active**.

---

## ÉTAPE 4 : GÉNÉRATION DU PDF AVEC QR CODE

### 4.1. Accès au PDF
- Dans le tableau des factures normalisées
- Cliquez sur le bouton **View PDF** ou **Imprimer**

### 4.2. Contenu du PDF
Le PDF généré contient :
- Toutes les informations de la facture
- Détails du client
- Liste des articles/services
- Montants (HT, TVA, TTC)
- **QR Code DGI** (35mm x 35mm en bas à droite de la page)

### 4.3. QR Code
Le QR code contient les données cryptées :
- Code DEF DGI
- UID de la facture
- NIF de l'entreprise
- Montant TTC
- Date de normalisation

**Résultat** : PDF officiel conforme DGI avec QR code pour vérification.

---

## ÉTAPE 5 : CRÉATION D'UNE FACTURE D'AVOIR (FA) - RETOUR

### 5.1. Quand Créer une FA ?
Une facture d'avoir est créée dans les cas suivants :
- Retour de marchandises par le client
- Annulation partielle ou totale d'un service
- Correction d'une erreur de facturation
- Note de crédit

### 5.2. Conditions Préalables
**IMPORTANT** : Vous ne pouvez créer une FA que si :
- La facture de vente (FV) existe et est normalisée
- La FV a un `code_UID` et un `code_DEF_DGI`
- Aucune FA n'a déjà été créée pour cette facture

### 5.3. Accès au Bouton FA
- Dans le tableau **View Invoices**
- Section des factures normalisées (FV)
- Localisez le bouton **FA DGI** ou **CREDIT NOTE DGI**

### 5.4. Lancement de la Création FA
- Cliquez sur le bouton **FA DGI**
- Une fenêtre de confirmation apparaît :
  ```
  Voulez-vous créer une facture d'avoir (retour) pour cette facture ?
  Cette action créera une nouvelle facture DGI de type FA.

  [Annuler]  [Oui, Créer FA]
  ```
- Cliquez sur **Oui, Créer FA**

### 5.5. Processus de Création FA
Le système effectue automatiquement :

1. **Vérification** que la FV existe et n'a pas déjà de FA
2. **Préparation de la requête** JSON pour l'API DGI :
   ```json
   {
     "type": "FA",
     "reference": "TEST-FACT-URES-CTF3-TGCF-PMFZ",
     "referenceType": "RAM",
     "items": [{"price": 1000.0}]
   }
   ```
   - `reference` = le `code_DEF_DGI` de la FV originale
   - `referenceType` = "RAM" (obligatoire pour FA)
   - Prix en **valeurs positives**

3. **Envoi à l'API DGI** via POST /api/invoice
4. **Confirmation automatique** via PUT /api/invoice/{uid}/confirm
5. **Réception de la réponse FA** contenant :
   - **UID_FA** : Nouvel identifiant unique
   - **Code DEF FA** : Nouveau code définitif
   - Autres données FA

### 5.6. Enregistrement dans la Base de Données
Le système met à jour la même ligne dans `facture_dossier` :
```sql
UPDATE facture_dossier SET
  code_UID_FA = '[UID FA reçu]',
  code_DEF_DGI_FA = '[Code DEF FA reçu]',
  date_DGI_FA = '[Date et heure actuelle]',
  type_facture_DGI_FA = 'FA',
  nim_DGI_FA = '[NIM FA]',
  compteur_DGI_FA = '[Compteur FA]',
  qrcode_string_DGI_FA = '[Données QR FA]'
WHERE ref_fact = '[Référence facture]'
```

**IMPORTANT** : La même facture a maintenant :
- Les champs FV remplis (code_UID, code_DEF_DGI, etc.)
- Les champs FA remplis (code_UID_FA, code_DEF_DGI_FA, etc.)

### 5.7. Confirmation Visuelle
- Message de succès avec UID et Code DEF de la FA
- La facture apparaît avec l'indication **FV + FA** (deux normalisations)

**Résultat** : La facture a maintenant une FV ET une FA, toutes deux normalisées DGI.

---

## ÉTAPE 6 : GÉNÉRATION DES RAPPORTS STATISTIQUES DGI

### 6.1. Concept de Session Fiscale
Avant de générer les rapports, comprenez le concept de **session** :
- Une session démarre automatiquement au premier rapport
- Elle reste **active** jusqu'à la génération d'un Z-Rapport
- Toutes les factures normalisées pendant la session sont comptabilisées

---

## ÉTAPE 7 : X-RAPPORT (RAPPORT DE CONSULTATION)

### 7.1. Objectif
Consulter les statistiques de vente **sans fermer la session**. Peut être généré plusieurs fois par jour.

### 7.2. Accès au X-Rapport
- Menu de gauche : **Rapports Statistiques DGI** → **X-Rapport**

### 7.3. Génération Mode Quotidien
1. La page s'ouvre avec les statistiques actuelles affichées :
   - Nombre de factures normalisées depuis le début de la session
   - Montant total HT
   - Montant total TVA
   - Montant total TTC

2. Cliquez sur **Générer X-Rapport**

3. Le système effectue :
   - Récupération de toutes les FV depuis le début de la session
   - Récupération de toutes les FA depuis le début de la session
   - Calcul des montants (conversion devise, TVA)
   - Agrégation par type, taxation, paiement

4. Le rapport s'affiche avec plusieurs onglets :
   - **Synthèse** : Vue d'ensemble
   - **Par Type** : FV vs FA
   - **Par Taxation** : Groupe A (0%) vs Groupe B (16%)
   - **Par Paiement** : Espèces, virement, etc.
   - **Liste Factures** : Détail complet

### 7.4. Génération Mode Périodique
1. Sélectionnez **Mode Périodique**
2. Choisissez la date de début
3. Choisissez la date de fin
4. Cliquez sur **Générer X-Rapport Périodique**

### 7.5. Enregistrement
Le rapport est automatiquement archivé dans la table `rapports_dgi` :
```sql
INSERT INTO rapports_dgi (
  type_rapport = 'X',
  numero_rapport = 'X-2025-1203093045',
  id_session = [ID session active],
  periode_debut = [Début session],
  periode_fin = [Maintenant],
  donnees_rapport = [JSON complet],
  nbre_factures = [Nombre total],
  montant_total = [Montant TTC]
)
```

### 7.6. Actions Disponibles
- **Rafraîchir** : Mettre à jour les données
- **Imprimer** : Imprimer le rapport
- **Exporter Excel** : Télécharger en Excel

**Résultat** : Rapport consultatif généré, session toujours active, aucun changement.

---

## ÉTAPE 8 : Z-RAPPORT (RAPPORT DE CLÔTURE)

### 8.1. Objectif
**Clôturer définitivement** la session fiscale active. Généralement fait **une fois par jour** en fin de journée.

### 8.2. ⚠️ ATTENTION - Action Irréversible
- Le Z-Rapport ferme la session définitivement
- Crée automatiquement une nouvelle session
- **Ne peut pas être annulé**

### 8.3. Accès au Z-Rapport
- Menu de gauche : **Rapports Statistiques DGI** → **Z-Rapport**

### 8.4. Vérification Préalable
Avant de clôturer, vérifiez :
1. Toutes les factures du jour sont-elles normalisées ?
2. Tous les retours (FA) sont-ils traités ?
3. Les montants affichés sont-ils corrects ?
4. Avez-vous consulté un X-Rapport pour vérifier ?

### 8.5. Génération du Z-Rapport
1. La page affiche :
   - Numéro de session active (ex: Z-2025-015)
   - Date et heure de début de session
   - Statistiques complètes

2. Cliquez sur **Générer Z-Rapport et Clôturer**

3. Une fenêtre d'avertissement apparaît :
   ```
   ⚠️ ATTENTION - ACTION DÉFINITIVE ⚠️

   Vous êtes sur le point de clôturer la session Z-2025-015.
   Cette action est IRRÉVERSIBLE et va :
   - Fermer définitivement la session actuelle
   - Créer un nouveau rapport Z définitif
   - Ouvrir automatiquement une nouvelle session

   Êtes-vous absolument certain ?

   [Annuler]  [Oui, Clôturer Définitivement]
   ```

4. Cliquez sur **Oui, Clôturer Définitivement**

### 8.6. Processus de Clôture
Le système effectue automatiquement :

1. **Récupération de toutes les factures** de la session active
2. **Calcul des statistiques finales** (mêmes calculs que X-Rapport)
3. **Mise à jour de la session** dans `sessions_dgi` :
   ```sql
   UPDATE sessions_dgi SET
     statut_session = 'cloturee',
     date_fin = NOW(),
     date_cloture = NOW(),
     nbre_factures_total = [Total],
     nbre_factures_fv = [Nombre FV],
     nbre_factures_fa = [Nombre FA],
     montant_total_ttc = [Montant total]
   WHERE id_session = [Session active]
   ```

4. **Enregistrement du Z-Rapport** :
   ```sql
   INSERT INTO rapports_dgi (
     type_rapport = 'Z',
     numero_rapport = 'Z-2025-015',
     id_session = [ID session clôturée],
     periode_debut = [Début session],
     periode_fin = [Fin session],
     donnees_rapport = [JSON complet]
   )
   ```

5. **Création d'une nouvelle session** :
   ```sql
   INSERT INTO sessions_dgi (
     numero_session = 'Z-2025-016',
     date_debut = NOW(),
     statut_session = 'active'
   )
   ```

### 8.7. Confirmation
- Message de succès :
  ```
  Session Z-2025-015 clôturée avec succès !
  Nouvelle session Z-2025-016 ouverte.

  Statistiques de la session clôturée :
  - Nombre de factures : 45
  - Montant total : 125,850.00 USD
  ```

- Le Z-Rapport s'affiche et peut être :
  - Imprimé
  - Exporté en PDF
  - Exporté en Excel

**Résultat** : Session clôturée, Z-Rapport archivé, nouvelle session active.

---

## ÉTAPE 9 : A-RAPPORT (RAPPORT DES ARTICLES)

### 9.1. Objectif
Rapport détaillé **par article ou service** vendu pendant une période.

### 9.2. Accès au A-Rapport
- Menu de gauche : **Rapports Statistiques DGI** → **A-Rapport**

### 9.3. Génération Mode Automatique
1. Cliquez sur **Générer A-Rapport Automatique**

2. Le système :
   - Cherche le dernier A-Rapport généré
   - Calcule depuis la date de fin du dernier rapport
   - Jusqu'à aujourd'hui

3. Si aucun A-Rapport précédent :
   - Génère depuis la première facture normalisée

### 9.4. Génération Mode Périodique
1. Sélectionnez **Mode Périodique**
2. Choisissez la date de début
3. Choisissez la date de fin
4. Cliquez sur **Générer A-Rapport Périodique**

### 9.5. Processus de Génération
Le système effectue :

1. **Récupération des factures** de la période (FV et FA)
2. **Extraction des détails** de chaque facture
3. **Agrégation par article** :
   ```
   Pour chaque article :
   - Code article
   - Nom article
   - Type (SER, RAM, BEN)
   - Prix unitaire
   - Taux TVA
   - Quantité vendue (depuis FV)
   - Quantité retournée (depuis FA)
   - Montant ventes
   - Montant retours
   - Montant net
   ```

4. **Calcul des totaux globaux**

### 9.6. Affichage du Rapport
Tableau détaillé avec toutes les colonnes :

| Code | Nom | Type | Prix | TVA | Qté Vendue | Qté Retour | Ventes | Retours | Net |
|------|-----|------|------|-----|------------|------------|--------|---------|-----|
| TRANS-001 | Transport | SER | 500.00 | 16% | 10 | 2 | 5,800.00 | 1,160.00 | 4,640.00 |
| DECLA-001 | Déclaration | SER | 200.00 | 0% | 8 | 0 | 1,600.00 | 0.00 | 1,600.00 |

### 9.7. Enregistrement
```sql
INSERT INTO rapports_dgi (
  type_rapport = 'A',
  numero_rapport = 'A-2025-1203093045',
  id_session = NULL,
  periode_debut = [Date début],
  periode_fin = [Date fin],
  donnees_rapport = [JSON avec détails articles],
  nbre_factures = [Nombre d'articles],
  montant_total = [Montant net total]
)
```

### 9.8. Actions Disponibles
- **Imprimer** : Imprimer le rapport
- **Exporter Excel** : Télécharger en Excel

**Résultat** : Rapport détaillé par article généré et archivé.

---

## ÉTAPE 10 : HISTORIQUE ET CONSULTATION

### 10.1. Accès à l'Historique
- Menu de gauche : **Rapports Statistiques DGI** → **Historique Sessions**

### 10.2. Vue des Sessions
Liste de toutes les sessions fiscales :
- Sessions clôturées (avec dates de début et fin)
- Session active (en cours)
- Statistiques par session
- Nombre de rapports générés par session

### 10.3. Vue des Rapports
Liste de tous les rapports générés :
- Type (X, Z, ou A)
- Numéro de rapport
- Date et heure de génération
- Utilisateur ayant généré
- Période couverte
- Nombre de factures/articles
- Montant total

### 10.4. Actions sur Rapports Archivés
Pour chaque rapport, vous pouvez :
- **Consulter** les détails complets
- **Réimprimer** le rapport
- **Exporter** en Excel ou PDF

**Résultat** : Traçabilité complète de toutes les opérations DGI.

---

## RÉCAPITULATIF DU FLUX COMPLET

```
1. CRÉATION FACTURE
   └─> Statut : En Attente

2. VALIDATION FACTURE
   └─> Statut : Validée

3. NORMALISATION FV DGI
   └─> Statut : FV Active
   └─> Base de données : code_UID, code_DEF_DGI remplis

4. GÉNÉRATION PDF avec QR CODE
   └─> Document officiel DGI

5. CRÉATION FA (si retour)
   └─> Statut : FV + FA Actives
   └─> Base de données : code_UID_FA, code_DEF_DGI_FA remplis

6. X-RAPPORT (consultation, plusieurs fois)
   └─> Session : Toujours active
   └─> Archive : Table rapports_dgi

7. Z-RAPPORT (clôture, une fois)
   └─> Session actuelle : Clôturée
   └─> Nouvelle session : Créée automatiquement
   └─> Archive : Table rapports_dgi + sessions_dgi

8. A-RAPPORT (articles, sur demande)
   └─> Détails par article/service
   └─> Archive : Table rapports_dgi

9. HISTORIQUE
   └─> Consultation de tous les rapports archivés
```

---

## POINTS CLÉS À RETENIR

### Architecture des Données
- **Une facture** = Une ligne dans `facture_dossier`
- **Champs FV** : code_UID, code_DEF_DGI, date_DGI, etc.
- **Champs FA** : code_UID_FA, code_DEF_DGI_FA, date_DGI_FA, etc.
- **États possibles** :
  - Non normalisée : code_UID = NULL, code_UID_FA = NULL
  - FV seule : code_UID rempli, code_UID_FA = NULL
  - FA seule : code_UID = NULL, code_UID_FA rempli
  - FV + FA : Les deux remplis

### Normalisation DGI
- **FV** : Facture de vente normale, referenceType = "BON"
- **FA** : Facture d'avoir (retour), referenceType = "RAM"
- **FA nécessite** : Une FV préalable avec code_DEF_DGI
- **Prix FA** : Toujours en valeurs positives dans la requête API

### Sessions Fiscales
- **Une session** = Période entre deux Z-Rapports
- **Session active** : Une seule à la fois
- **Ouverture** : Automatique au premier X-Rapport
- **Clôture** : Via Z-Rapport (irréversible)

### Rapports
- **X-Rapport** : Consultation, illimitée, aucun effet
- **Z-Rapport** : Clôture, une fois par session, ferme la session
- **A-Rapport** : Articles, illimité, indépendant des sessions

### Conformité DGI
- ✅ Seules les factures normalisées apparaissent dans les rapports
- ✅ FV et FA sont comptées séparément
- ✅ Tous les rapports sont archivés
- ✅ Traçabilité complète (qui, quand, quoi)
- ✅ QR codes sur tous les PDF

---
