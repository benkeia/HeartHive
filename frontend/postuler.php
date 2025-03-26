// Après avoir traité la candidature avec succès:
fetch('../backend/xp.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
  body: 'action=apply_association&details=association_' + association_id
})
.then(response => response.json())
.then(data => {
  if (data.status === 'success') {
    showXPNotification(data.points, "Candidature envoyée!");
  }
});