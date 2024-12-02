import sys
import json
import os
import urllib.parse

GIFTS = [
    "Book", "Toy", "Gadget", "Video Game", "Headphones",
    "Smartphone", "Laptop", "Watch", "Shoes", "Wallet",
    "Headset", "Camera", "Drone", "Smart Watch", "Bluetooth Speaker"
]

def list_gifts():
    """Imprime la lista de regalos como JSON."""
    gifts_with_keys = [{"key": index, "value": gift} for index, gift in enumerate(GIFTS)]
    print(json.dumps(gifts_with_keys))

def generate_result(indices):
    """Genera la respuesta JSON con los regalos seleccionados y un código único."""
    selected_gifts = [GIFTS[i] for i in indices]
    gift_code = sum(1 << i for i in indices)

    result = {
        "selected_gifts": ", ".join(selected_gifts),
        "unique_code": gift_code
    }
    print(json.dumps(result))

def main():
    if len(sys.argv) > 1 and sys.argv[1] == "list":
        list_gifts()
        return

    input_data = sys.stdin.read()
    if input_data:
        try:
            data = json.loads(input_data)
            selected_indices = data.get("gifts", "")
            indices = [int(i) for i in selected_indices.split(",") if i.isdigit()]
            generate_result(indices)
        except Exception as e:
            print(json.dumps({"error": str(e)}))
    else:
        print(json.dumps({"error": "No input data provided"}))

if __name__ == "__main__":
    main()
