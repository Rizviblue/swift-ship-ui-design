import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { MoreHorizontal, Eye, Edit, Trash2 } from "lucide-react";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

interface Courier {
  id: string;
  trackingNumber: string;
  sender: string;
  receiver: string;
  destination: string;
  status: 'pending' | 'in-transit' | 'delivered' | 'cancelled';
  createdAt: string;
}

interface RecentCouriersProps {
  couriers: Courier[];
}

const statusConfig = {
  pending: { label: "Pending", className: "bg-warning/10 text-warning border-warning/20" },
  'in-transit': { label: "In Transit", className: "bg-primary/10 text-primary border-primary/20" },
  delivered: { label: "Delivered", className: "bg-success/10 text-success border-success/20" },
  cancelled: { label: "Cancelled", className: "bg-destructive/10 text-destructive border-destructive/20" },
};

export function RecentCouriers({ couriers }: RecentCouriersProps) {
  return (
    <div className="glass-card rounded-xl p-6 animate-slide-up">
      <div className="flex items-center justify-between mb-6">
        <h3 className="text-lg font-semibold text-foreground">Recent Couriers</h3>
        <Button variant="outline" size="sm">View All</Button>
      </div>
      
      <div className="space-y-4">
        {couriers.map((courier) => (
          <div
            key={courier.id}
            className="flex items-center justify-between p-4 rounded-lg border border-border bg-background/50 hover:bg-muted/50 transition-colors"
          >
            <div className="flex-1 min-w-0">
              <div className="flex items-center space-x-3">
                <div className="flex-1">
                  <p className="text-sm font-medium text-foreground">#{courier.trackingNumber}</p>
                  <p className="text-xs text-muted-foreground">{courier.sender} → {courier.receiver}</p>
                </div>
                <div className="text-right">
                  <p className="text-sm text-muted-foreground">{courier.destination}</p>
                  <p className="text-xs text-muted-foreground">{courier.createdAt}</p>
                </div>
              </div>
            </div>
            
            <div className="flex items-center space-x-3 ml-4">
              <Badge className={statusConfig[courier.status].className}>
                {statusConfig[courier.status].label}
              </Badge>
              
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="ghost" size="sm">
                    <MoreHorizontal className="w-4 h-4" />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" className="w-48">
                  <DropdownMenuItem>
                    <Eye className="w-4 h-4 mr-2" />
                    View Details
                  </DropdownMenuItem>
                  <DropdownMenuItem>
                    <Edit className="w-4 h-4 mr-2" />
                    Edit
                  </DropdownMenuItem>
                  <DropdownMenuItem className="text-destructive">
                    <Trash2 className="w-4 h-4 mr-2" />
                    Delete
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}