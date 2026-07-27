// Medidor de força de senha — heurística simples baseada em:
// comprimento, letras minúsculas/maiúsculas, números e símbolos.
// Sem dependência externa, só JS puro.

(function(){

    const input = document.getElementById('password_input');
    const bar = document.getElementById('password_strength_bar');
    const label = document.getElementById('password_strength_label');

    if(!input || !bar || !label){
        return;
    }

    function calculateStrength(password){

        let score = 0;

        if(password.length >= 6) score++;
        if(password.length >= 10) score++;
        if(/[a-z]/.test(password)) score++;
        if(/[A-Z]/.test(password)) score++;
        if(/[0-9]/.test(password)) score++;
        if(/[^a-zA-Z0-9]/.test(password)) score++;

        return score;
    }

    function updateUI(password){

        if(!password.length){
            bar.style.width = '0%';
            bar.className = 'progress-bar';
            label.textContent = '';
            return;
        }

        const score = calculateStrength(password);

        let percent, className, text;

        if(score <= 2){
            percent = 33;
            className = 'progress-bar bg-danger';
            text = 'Senha fraca';
        }else if(score <= 4){
            percent = 66;
            className = 'progress-bar bg-warning';
            text = 'Senha média';
        }else{
            percent = 100;
            className = 'progress-bar bg-success';
            text = 'Senha forte';
        }

        bar.style.width = percent + '%';
        bar.className = className;
        label.textContent = text;
    }

    input.addEventListener('input', function(){
        updateUI(this.value);
    });

})();
