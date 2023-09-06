#[Route('/spot/search', name: 'spot_search', methods: ['POST'])]
public function search(Request $request, SpotRepository $spotRepository, SerializerInterface $serializer)
{
$spots = [];
//création d'une instance vierge du formulaire
/*"Pourquoi ne pas simplement extraire les données de la Request directement ?" La raison est que le formulaire offre de nombreux avantages :
Validation : Les contraintes de validation que vous avez définies pour votre formulaire seront automatiquement vérifiées lorsque vous appelez $form->isValid().
Protection contre les attaques : Les formulaires de Symfony offrent une protection intégrée contre les attaques CSRF.
Transformation des données : Les formulaires permettent de transformer automatiquement les données entre les valeurs soumises et les valeurs utilisées dans votre modèle (par exemple, transformer une chaîne en une DateTime).
Mappage d'objet : Si vous avez lié un objet à votre formulaire (par exemple, une entité Doctrine), Symfony peuplera automatiquement cet objet avec les données soumises.
*/
$formSearch = $this->createForm(SpotSearchType::class);
$formSearch->handleRequest($request); // reçoit les parametres POST

if ($formSearch->isSubmitted() && $formSearch->isValid()) {
dd("ok");
$formData = $formSearch->getData();
$name = $formData['name'] ?? null;
$moduleNames = $formData['modules'] ?? [];

$spots = $spotRepository->findByModules($name, $moduleNames);

if ($request->isXmlHttpRequest()) {
$jsonSpots = $serializer->serialize($spots, 'json');
return new JsonResponse($jsonSpots);
}
}
}