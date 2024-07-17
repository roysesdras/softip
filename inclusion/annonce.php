            
            
            <?php foreach ($annonces as $annonce): ?>
                <div class="col-md-12 mb-1 evene">
                    <div class="ca" id="card">
                        <div class="card-body">
                            <p class="card-text"><?php echo htmlspecialchars($annonce['description']); ?></p>
                            <!-- <a href="#" class="button">En savoir plus</a> -->
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>