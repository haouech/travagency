
Binome :
  Majdi HAOUECH
	Hamza HASSINE

User :
	anna_admin
	john_user


Les Routes :
----------------------------------- ---------- -------- ---------------------------------------------
  Name                                Method     Role	   Path
 ----------------------------------- ---------- -------- --------------------------------------------

  circuit_index                       GET        ANY      /circuit/
  circuit_show                        ANY        ANY      /circuit/{id}
  admin_circuit_new                   ANY        admin    /circuit/new
  admin_circuit_edit                  ANY        admin    /circuit/{id}/edit
  delete_id                           GET        admin    /delete/{id}
  homepage                            ANY        ANY      /
  contact                             ANY        ANY      /contact
  new_etape                           ANY        admin    /circuit/{id}/new_etape
  edit_etape                          ANY        admin    /circuit/{id}/edit_etape/{etape_id}
  delete_etape                        ANY        admin    /circuit/{id}/delete_etape/{etape_id}
  new_prog                            ANY        admin    /circuit/{id}/new_prog
  edit_prog                           ANY        admin    /circuit/{id}/edit_prog/{prog_id}
  delete_prog                         ANY        admin    /circuit/{id}/delete_prog/{prog_id}
  fos_user_security_login             GET|POST   ANY      /login
  fos_user_security_logout            GET|POST   ANY      /logout

 ----------------------------------- ---------- -------- ------ ---------------------------------------

Manuel d'utilisation :

Un Utilisateur Anonyme peut :
1) avoir une id�e sur les Actualit�s et les Objectifs de l'entreprise
  2) Consulter le catalogue des circuits
  3) Consulter les d�tails de Chaque Circuit
  4) Nous contacter


Un utilisateur connect� peut :
1) avoir une id�e sur les Actualit�s et les Objectifs de l'entreprise
  2) Consulter le catalogue des circuits
  3) Consulter les d�tails de Chaque Circuit
  4) Nous contacter
  5) Ajouter un Commentaire sur chaque circuit

Un administrateur ou un collaborateur peut :
  1) Avoir acces � tous les onglets
  2) Consulter les circuits et les d�tails
  3) Ajouter , Modifier et supprimer un circuit
  4) Confirmer ou d�programmer un circuit
  5) Ajouter , Modifier et supprimer une �tape
  6) Ajouter , Modifier et supprimer une un programme
  7) Ajouter un Commentaire

Remarques :

1) On a fait un controle d'accès au niveau du fichier security.yml

2) Les utilisateurs ( autre que l'Admin )  peuvent consulter les circuit qui sont confirm�s par l'Admin et qui ont au moins un programme

3) Un circuit aura par d�faut aux moins deux �tapes : la ville de d�part et la ville d'arriv�

4) Une fois on modifie les �tapes d'un circuit , le circuit se met � jour automatiquement :
  - La liste des étapes sera toujours triée par ordre ascendant du numéro
	- Ville de d�part aura comme valeur la premi�re �tape
	- Ville d'arriv� aura comme valeur la derni�re �tape
	- dur�e d'un circuit aura comme valeur la somme des dur�es de toutes les �tapes
