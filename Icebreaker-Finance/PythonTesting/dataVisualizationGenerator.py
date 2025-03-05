import sys
import json
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import pandas as pd
import io
import base64


class DefinePlot:
    def __init__(self, data=[], columns=[]):
        self.data = data
        self.columns = columns
        self.dataframe = pd.DataFrame(data, columns=columns)
        self.columnNames = list(self.dataframe.columns)
        self.fig = None

    def getImageBase64(self):
        if self.fig:
            buf = io.BytesIO()
            self.fig.savefig(buf, format='png')
            buf.seek(0)
            return base64.b64encode(buf.getvalue()).decode('utf-8')
        else:
            return "No plot has been created yet."

    def line(self, xColumnName, yColumnName, title='', xlabel='', ylabel='', color='blue', linewidth=2, marker='o'):
        self.fig, ax = plt.subplots()
        ax.plot(self.dataframe[xColumnName], self.dataframe[yColumnName], color=color, linewidth=linewidth, marker=marker)
        ax.set_title(title)
        ax.set_xlabel(xlabel)
        ax.set_ylabel(ylabel)

def validate_json(raw_input):
    try:
        return json.loads(raw_input)
    except json.JSONDecodeError as e:
        print(f"Invalid JSON: {e}")
        print(f"Raw input received: {raw_input}")
        sys.exit(1)

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Error: No input provided")
        sys.exit(1)

    try:
        # Get and validate input
        raw_input = sys.argv[1]
        input_data = validate_json(raw_input)

        # Rest of plotting logic
        plotter = DefinePlot(input_data['data'], input_data['columns'])
        plot_method = getattr(plotter, input_data['plot_type'])
        plot_method(**input_data['plot_args'])
        print(plotter.getImageBase64())

    except Exception as e:
        print(f"Error: {e}")
        sys.stderr.write(f"FULL ERROR: {str(e)}\n")
        sys.exit(1)
