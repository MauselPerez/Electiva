# Función que representa el integrando
def f(x):
    return 4 / (1 + x**2)

# Número de divisiones para el método de Simpson
num_divisiones = 1000

# Ancho de cada división
ancho = 1 / num_divisiones

# Inicialización de la suma
suma = 0

# Bucle para calcular la suma usando el método de Simpson
for i in range(num_divisiones):
    x0 = i * ancho
    x1 = (i + 0.5) * ancho
    x2 = (i + 1) * ancho
    
    suma += (f(x0) + 4 * f(x1) + f(x2)) * ancho / 6

# Imprime el resultado aproximado de la integral
print("El valor aproximado de la integral es:", suma)
