// Caminho do arquivo Word a ser carregado automaticamente
const guiaoFilePath = "./guiao.docx";  // Certifique-se de que o arquivo está neste caminho

// Função para carregar e exibir o conteúdo do Word
function loadDocx(filePath) {
    console.log(`Tentando carregar o arquivo: ${filePath}`);  // Log para verificar o caminho

    fetch(filePath)
        .then(response => {
            if (!response.ok) {
                // Se o arquivo não for encontrado ou se houver outro erro no fetch
                throw new Error(`Erro ao acessar o arquivo: ${response.statusText}`);
            }
            console.log("Arquivo carregado com sucesso! Status:", response.status);
            return response.arrayBuffer();
        })
        .then(arrayBuffer => {
            console.log("Arquivo convertido para ArrayBuffer com sucesso!");  // Confirmação da conversão

            // Usando o mammoth.js para converter o arquivo DOCX para HTML
            mammoth.convertToHtml({ arrayBuffer: arrayBuffer })
                .then(result => {
                    console.log("Conversão do arquivo Word para HTML foi bem-sucedida!");  // Log da conversão

                    // Obtendo o HTML convertido
                    const htmlContent = result.value;

                    // Inserindo o HTML convertido na página
                    document.getElementById("guião").innerHTML = htmlContent;

                    // Garantindo que o conteúdo seja não-editável
                    document.getElementById("guião").contentEditable = "false";
                })
                .catch(err => {
                    console.error("Erro ao processar o arquivo Word:", err);
                    document.getElementById("guião").innerHTML = "<p>Erro ao processar o arquivo Word.</p>";
                });
        })
        .catch(err => {
            console.error("Erro ao carregar o arquivo:", err);
            document.getElementById("guião").innerHTML = `<p>Erro ao acessar o arquivo do guião. ${err.message}</p>`;
        });
}

// Carregar o arquivo automaticamente ao iniciar a página
window.onload = () => {
    loadDocx(guiaoFilePath);
};
