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
        # Convert to DataFrame and ensure datetime sorting
        self.dataframe = pd.DataFrame(data, columns=columns)
        self.dataframe["Date"] = pd.to_datetime(self.dataframe["Date"])
        self.dataframe["Amount"] = pd.to_numeric(self.dataframe["Amount"])
        self.dataframe = self.dataframe.sort_values("Date")  # Ensure chronological order
        self.fig = None

    def getImageBase64(self):
        if self.fig:
            buf = io.BytesIO()
            self.fig.savefig(buf, format='png', bbox_inches='tight')
            buf.seek(0)
            return base64.b64encode(buf.getvalue()).decode('utf-8')
        else:
            return "No plot created."

    def line(self, xColumnName, yColumnName, title='', xlabel='', ylabel='', color='blue', linewidth=2, marker='o'):
        self.fig, ax = plt.subplots(figsize=(12, 6))

        # Use index for x-axis positions
        x_values = range(len(self.dataframe))
        dates = self.dataframe[xColumnName].dt.strftime('%Y-%m-%d').tolist()

        # Plot with numerical x-values
        ax.plot(
            x_values,
            self.dataframe[yColumnName],
            color=color,
            linewidth=linewidth,
            marker=marker,
            markersize=8
        )

        # Configure axis labels and title
        ax.set_title(title, fontsize=14)
        ax.set_xlabel(xlabel, fontsize=12)
        ax.set_ylabel(ylabel, fontsize=12)

        # Set x-ticks to show all dates
        ax.set_xticks(x_values)
        ax.set_xticklabels(dates, rotation=40, ha='right', fontsize=10)

        # Add value annotations
        for i, (x, y) in enumerate(zip(x_values, self.dataframe[yColumnName])):
            ax.annotate(
                f"${y:.2f}",
                (x, y),
                textcoords="offset points",
                xytext=(0, 10),
                ha='center',
                fontsize=9,
                color='darkblue'
            )

        # Add grid and styling
        ax.grid(True, linestyle='--', alpha=0.7)
        ax.spines['top'].set_visible(False)
        ax.spines['right'].set_visible(False)
        plt.tight_layout()

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Error: No input provided")
        sys.exit(1)

    try:
        # Process input data
        raw_input = sys.argv[1]
        rows = [row.split(",") for row in raw_input.split("$") if row]

        # Create plotter instance
        plotter = DefinePlot(rows, ["Date", "Amount"])
        plotter.line(
            "Date",
            "Amount",
            title="Debt Balance Timeline",
            xlabel="Transaction Dates",
            ylabel="Balance Amount ($)"
        )

        # Output image
        print(plotter.getImageBase64())

    except Exception as e:
        print(f"Error: {str(e)}")
        sys.exit(1)
